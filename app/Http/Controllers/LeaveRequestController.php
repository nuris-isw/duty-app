<?php

namespace App\Http\Controllers;

use App\Models\LeaveType;
use App\Models\LeaveRequest;
use App\Models\User;
use App\Models\UserLeaveQuota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB; // Tambahkan ini
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Mail\LeaveRequestStatusUpdated;
use App\Mail\NewLeaveRequestForSuperior;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class LeaveRequestController extends Controller
{
    use AuthorizesRequests;

    public function create()
    {
        $leaveTypes = LeaveType::all();
        return view('leave-requests.create', compact('leaveTypes'));
    }

    public function store(Request $request)
    {
        // 1. Validasi Input Dasar Dulu
        $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'reason'     => 'required|string',
        ]);

        $leaveType = LeaveType::findOrFail($request->leave_type_id);
        $user = Auth::user();

        // Aturan Khusus Sakit
        if ($leaveType->nama_cuti === 'Izin Sakit') {
            $request->merge(['reason' => 'Izin Sakit']);
        }

        // 2. Validasi Lanjutan
        $rules = []; // Rules dasar sudah divalidasi di atas
        if (!$leaveType->bisa_retroaktif) {
            $rules['start_date'] = 'after_or_equal:today';
        }
        if ($leaveType->memerlukan_dokumen) {
            $rules['dokumen_pendukung'] = 'required|file|mimes:pdf,jpg,png|max:2048';
        }
        
        if (!empty($rules)) {
            $request->validate($rules);
        }

        // 3. Hitung Durasi (Centralized Logic)
        $duration = $this->calculateDuration($request->start_date, $request->end_date);

        // Gunakan Transaction untuk Integritas Data
        try {
            return DB::transaction(function () use ($request, $user, $leaveType, $duration) {
                
                // Pengecekan Kuota
                if ($leaveType->kuota > 0) {
                    // Ambil kuota user (lock for update untuk mencegah race condition)
                    $userQuota = UserLeaveQuota::firstOrCreate(
                        ['user_id' => $user->id, 'leave_type_id' => $leaveType->id, 'tahun' => now()->year],
                        ['jumlah_diambil' => 0]
                    );

                    $sisaKuota = $leaveType->kuota - $userQuota->jumlah_diambil;
                    
                    if ($duration > $sisaKuota) {
                        // Throw exception agar ditangkap catch di bawah
                        throw new \Exception('Jatah cuti tidak mencukupi. Sisa: ' . $sisaKuota . ' hari.');
                    }
                }

                // Simpan Dokumen
                $documentPath = null;
                if ($request->hasFile('dokumen_pendukung')) {
                    $documentPath = $request->file('dokumen_pendukung')->store('attachments', 'public');
                }

                // Tentukan Status
                $statusDefault = 'pending';
                $approvedBy = null;
                $approvedAt = null;

                if (is_null($user->atasan_id)) {
                    $statusDefault = 'approved';
                    $approvedBy = $user->id;
                    $approvedAt = now();
                }

                // Simpan Pengajuan
                $leaveRequest = LeaveRequest::create([
                    'user_id' => $user->id,
                    'leave_type' => $leaveType->nama_cuti, // Saran: Sebaiknya simpan leave_type_id juga untuk relasi
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'reason' => $request->reason,
                    'dokumen_pendukung' => $documentPath,
                    'status' => $statusDefault,
                    'approved_by' => $approvedBy,
                    'approved_at' => $approvedAt,
                ]);

                // Potong kuota jika auto-approve
                if ($statusDefault === 'approved') {
                    // Kita oper $duration agar tidak perlu hitung ulang
                    $this->deductQuota($leaveRequest, $duration);
                }

                // Kirim Email (Queue)
                if ($statusDefault === 'pending' && $user->atasan_id) {
                    Mail::to($user->superior->email)->queue(new NewLeaveRequestForSuperior($leaveRequest));
                }

                return redirect()->route('dashboard')->with('success', 'Pengajuan cuti berhasil dikirim.');
            });

        } catch (\Exception $e) {
            // Tangkap error kuota atau error database
            return back()->withErrors(['start_date' => $e->getMessage()])->withInput();
        }
    }

    public function approve(LeaveRequest $leaveRequest)
    {
        $this->authorize('update', $leaveRequest);

        DB::transaction(function () use ($leaveRequest) {
            $leaveRequest->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

            // Hitung durasi di sini untuk dipassing ke deductQuota
            $duration = $this->calculateDuration($leaveRequest->start_date, $leaveRequest->end_date);
            $this->deductQuota($leaveRequest, $duration);
        });

        // Email ditaruh di luar transaksi agar antrian tidak tertahan lock database
        Mail::to($leaveRequest->user->email)->queue(new LeaveRequestStatusUpdated($leaveRequest));

        return redirect()->route('dashboard')->with('success', 'Pengajuan berhasil disetujui.');
    }

    public function reject(Request $request, LeaveRequest $leaveRequest)
    {
        $this->authorize('update', $leaveRequest);
        
        $validatedData = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $leaveRequest->update([
            'status' => 'rejected',
            'rejection_reason' => $validatedData['rejection_reason'],
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        Mail::to($leaveRequest->user->email)->queue(new LeaveRequestStatusUpdated($leaveRequest));
        
        return redirect()->route('dashboard')->with('success', 'Pengajuan berhasil ditolak.');
    }

    public function print(LeaveRequest $leaveRequest)
    {
        if (Auth::id() !== $leaveRequest->user_id && Auth::user()->role !== 'admin') {
            abort(403, 'Akses Ditolak');
        }

        $pdf = PDF::loadView('leave-requests.pdf', ['leaveRequest' => $leaveRequest]);
        return $pdf->stream('surat-izin-'.$leaveRequest->id.'.pdf');
    }
    
    /**
     * Helper untuk menghitung durasi hari kerja
     */
    private function calculateDuration($start, $end)
    {
        $startDate = Carbon::parse($start);
        $endDate = Carbon::parse($end);
        
        return $startDate->diffInDaysFiltered(function (Carbon $date) {
            return !$date->isWeekend();
        }, $endDate) + ($startDate->isWeekend() ? 0 : 1);
    }

    /**
     * Memotong kuota cuti.
     * Menerima $duration opsional untuk efisiensi.
     */
    private function deductQuota(LeaveRequest $leaveRequest, $duration = null)
    {
        $leaveType = LeaveType::where('nama_cuti', $leaveRequest->leave_type)->first();

        if ($leaveType && $leaveType->kuota > 0) {
            // Jika duration belum dihitung (misal dipanggil dari konteks lain), hitung dulu
            if (is_null($duration)) {
                $duration = $this->calculateDuration($leaveRequest->start_date, $leaveRequest->end_date);
            }

            $quota = UserLeaveQuota::firstOrCreate(
                [
                    'user_id' => $leaveRequest->user_id, 
                    'leave_type_id' => $leaveType->id, 
                    'tahun' => Carbon::parse($leaveRequest->start_date)->year
                ],
                ['jumlah_diambil' => 0]
            );

            $quota->increment('jumlah_diambil', $duration);
        }
    }
}