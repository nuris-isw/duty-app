<?php

namespace App\Http\Controllers;

use App\Models\LeaveType;
use App\Models\LeaveRequest;
use App\Models\User;
use App\Models\UserLeaveQuota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Mail\LeaveRequestStatusUpdated;
use App\Mail\NewLeaveRequestForSuperior;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class LeaveRequestController extends Controller
{
    use AuthorizesRequests;
    /**
     * Menampilkan form untuk membuat pengajuan baru.
     */
    public function create()
    {
        // Ambil semua jenis cuti dari database untuk ditampilkan di dropdown
        $leaveTypes = LeaveType::all();
        return view('leave-requests.create', compact('leaveTypes'));
    }

    /**
     * Menyimpan pengajuan baru ke database.
     */
    public function store(Request $request)
    {
        // 1. Validasi Awal & Ambil Aturan Cuti
        $request->validate(['leave_type_id' => 'required|exists:leave_types,id']);
        $leaveType = LeaveType::find($request->leave_type_id);
        $user = Auth::user();

        // Atur nilai default SEBELUM validasi
        if ($leaveType->nama_cuti === 'Sakit') {
            $request->merge(['reason' => 'Sakit']);
        }

        // 2. Validasi Lanjutan Berdasarkan Aturan
        $rules = [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string',
        ];
        if (!$leaveType->bisa_retroaktif) {
            $rules['start_date'] .= '|after_or_equal:today';
        }
        if ($leaveType->memerlukan_dokumen) {
            $rules['dokumen_pendukung'] = 'required|file|mimes:pdf,jpg,png|max:2048';
        }
        $validatedData = $request->validate($rules);

        // 3. Pengecekan Kuota
        if ($leaveType->kuota > 0) {
            $startDate = Carbon::parse($validatedData['start_date']);
            $endDate = Carbon::parse($validatedData['end_date']);
            $duration = $startDate->diffInDaysFiltered(function (Carbon $date) {
                return !$date->isWeekend(); // Hanya hitung hari kerja
            }, $endDate) + ($startDate->isWeekend() ? 0 : 1);

            $quota = UserLeaveQuota::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'leave_type_id' => $leaveType->id,
                    'tahun' => $startDate->year
                ],
                [
                    'jumlah_diambil' => 0
                ]
            );
            
            $sisaKuota = $leaveType->kuota - $quota->jumlah_diambil;
            if ($duration > $sisaKuota) {
                return back()->withErrors(['start_date' => 'Jatah cuti tidak mencukupi. Sisa: ' . $sisaKuota . ' hari.'])->withInput();
            }
        }

        // 4. Simpan Dokumen
        $documentPath = null;
        if ($request->hasFile('dokumen_pendukung')) {
            $documentPath = $request->file('dokumen_pendukung')->store('attachments', 'public');
        }

        // 5. Tentukan Status Awal (Auto-Approve untuk Atasan Tertinggi)
        $statusDefault = 'pending';
        $approvedBy = null;
        $approvedAt = null;
        
        if (is_null($user->atasan_id)) {
            $statusDefault = 'approved';
            $approvedBy = $user->id;
            $approvedAt = now();
        }

        // 6. Simpan Pengajuan
        $leaveRequest = LeaveRequest::create([
            'user_id' => $user->id,
            'leave_type' => $leaveType->nama_cuti,
            'start_date' => $validatedData['start_date'],
            'end_date' => $validatedData['end_date'],
            'reason' => $validatedData['reason'],
            'dokumen_pendukung' => $documentPath,
            'status' => $statusDefault,
            'approved_by' => $approvedBy,
            'approved_at' => $approvedAt,
        ]);

        // Potong kuota jika pengajuan di-auto-approve
        if ($statusDefault === 'approved') {
            $this->deductQuota($leaveRequest);
        }

        // 7. Kirim Email ke Atasan (jika tidak auto-approve)
        if ($statusDefault === 'pending' && $user->atasan_id) {
            Mail::to($user->superior->email)->queue(new NewLeaveRequestForSuperior($leaveRequest));
        }

        // 8. Redirect ke dashboard dengan pesan sukses
        return redirect()->route('dashboard')->with('success', 'Pengajuan cuti berhasil dikirim.');
    }

    /**
     * Menyetujui pengajuan cuti.
     */
    public function approve(LeaveRequest $leaveRequest)
    {
        // Panggil Policy untuk otorisasi
        $this->authorize('update', $leaveRequest);

        $leaveRequest->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        // Kurangi jatah cuti jika perlu
        $this->deductQuota($leaveRequest);

        Mail::to($leaveRequest->user->email)->queue(new LeaveRequestStatusUpdated($leaveRequest));
        return redirect()->route('dashboard')->with('success', 'Pengajuan berhasil disetujui.');
    }

    /**
     * Menolak pengajuan cuti.
     */
    public function reject(Request $request, LeaveRequest $leaveRequest)
    {
        $this->authorize('update', $leaveRequest);
        
        $validatedData = $request->validate([
            'rejection_reason' => 'required|string|max:500', // Validasi alasan
        ]);

        $leaveRequest->update([
            'status' => 'rejected',
            'rejection_reason' => $validatedData['rejection_reason'], // Simpan alasan
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        Mail::to($leaveRequest->user->email)->queue(new LeaveRequestStatusUpdated($leaveRequest));
        return redirect()->route('dashboard')->with('success', 'Pengajuan berhasil ditolak.');
    }

    /**
     * Menyiapkan data dan men-generate PDF.
     */
    public function print(LeaveRequest $leaveRequest)
    {
        if (Auth::id() !== $leaveRequest->user_id && Auth::user()->role !== 'admin') {
            abort(403, 'Akses Ditolak');
        }

        $pdf = PDF::loadView('leave-requests.pdf', ['leaveRequest' => $leaveRequest]);
        return $pdf->stream('surat-izin-'.$leaveRequest->id.'.pdf');
    }
    
    /**
     * Method privat untuk memotong kuota cuti.
     */
    private function deductQuota(LeaveRequest $leaveRequest)
    {
        $leaveType = LeaveType::where('nama_cuti', $leaveRequest->leave_type)->first();

        // Cek apakah jenis cuti ini memiliki kuota yang harus dikurangi
        if ($leaveType && $leaveType->kuota > 0) {
            // Hitung durasi cuti (hanya hari kerja)
            $startDate = Carbon::parse($leaveRequest->start_date);
            $endDate = Carbon::parse($leaveRequest->end_date);
            $duration = $startDate->diffInDaysFiltered(function (Carbon $date) {
                return !$date->isWeekend();
            }, $endDate) + ($startDate->isWeekend() ? 0 : 1);

            // Cari atau buat data kuota user untuk tahun ini
            $quota = UserLeaveQuota::firstOrCreate(
                [
                    'user_id' => $leaveRequest->user_id, 
                    'leave_type_id' => $leaveType->id, 
                    'tahun' => $startDate->year
                ],
                ['jumlah_diambil' => 0]
            );

            // Tambahkan jumlah hari yang diambil
            $quota->increment('jumlah_diambil', $duration);
        }
    }
}