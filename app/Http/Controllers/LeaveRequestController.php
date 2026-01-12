<?php

namespace App\Http\Controllers;

use App\Models\LeaveType;
use App\Models\LeaveRequest;
use App\Models\User;
use App\Models\UserLeaveQuota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
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
        // --- 1. PRE-VALIDATION LOGIC (FIX BUG REASON) ---
        // Kita harus merge reason SEBELUM validasi dijalankan
        if ($request->has('leave_type_id')) {
            $leaveType = LeaveType::find($request->leave_type_id);
            // Jika Izin Sakit, otomatis isi alasan agar lolos validasi 'required'
            if ($leaveType && $leaveType->nama_cuti === 'Izin Sakit') {
                $request->merge(['reason' => 'Izin Sakit']);
            }
        }

        // --- 2. VALIDASI INPUT ---
        $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date'    => 'required|date',
            'end_date'      => 'required|date|after_or_equal:start_date',
            'reason'        => 'required|string', // Aman karena sudah di-merge di atas
        ]);

        $leaveType = LeaveType::findOrFail($request->leave_type_id);
        $user = Auth::user();

        // Validasi Aturan Tambahan dari Database
        if (!$leaveType->bisa_retroaktif) {
            $request->validate(['start_date' => 'after_or_equal:today']);
        }
        
        // Cek kolom 'memerlukan_dokumen' (boolean 1/0)
        if ($leaveType->memerlukan_dokumen == 1) {
            $request->validate([
                'dokumen_pendukung' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048'
            ]);
        }

        // --- 3. HITUNG DURASI (Dengan Logika Libur Nasional) ---
        $duration = $this->calculateDuration($request->start_date, $request->end_date);

        // --- 4. PROSES PENYIMPANAN (TRANSACTION) ---
        try {
            return DB::transaction(function () use ($request, $user, $leaveType, $duration) {
                
                // A. Pengecekan Kuota (Hanya jika kuota > 0, misal Cuti Tahunan)
                // Jika kuota 0 (seperti Izin Sakit/Dinas), dianggap Unlimited/Tidak Potong Cuti
                if ($leaveType->kuota > 0) {
                    // Gunakan lockForUpdate untuk mencegah race condition
                    $userQuota = UserLeaveQuota::where('user_id', $user->id)
                        ->where('leave_type_id', $leaveType->id)
                        ->where('tahun', now()->year)
                        ->lockForUpdate()
                        ->first();

                    // Jika belum ada record kuota, anggap pemakaian masih 0
                    $jumlahTerpakai = $userQuota ? $userQuota->jumlah_diambil : 0;
                    $sisaKuota = $leaveType->kuota - $jumlahTerpakai;
                    
                    if ($duration > $sisaKuota) {
                        throw new \Exception("Sisa cuti tidak mencukupi. Sisa: {$sisaKuota} hari, Pengajuan: {$duration} hari.");
                    }
                }

                // B. Upload Dokumen
                $documentPath = null;
                if ($request->hasFile('dokumen_pendukung')) {
                    $documentPath = $request->file('dokumen_pendukung')->store('attachments', 'public');
                }

                // C. Tentukan Status Awal
                // Jika tidak punya atasan, otomatis Approved
                $statusDefault = is_null($user->atasan_id) ? 'approved' : 'pending';
                $approvedBy = ($statusDefault === 'approved') ? $user->id : null;
                $approvedAt = ($statusDefault === 'approved') ? now() : null;

                // D. Simpan ke Database
                $leaveRequest = LeaveRequest::create([
                    'user_id'           => $user->id,
                    'leave_type'        => $leaveType->nama_cuti, // Menyimpan nama string (sesuai DB lama)
                    'start_date'        => $request->start_date,
                    'end_date'          => $request->end_date,
                    'reason'            => $request->reason,
                    'dokumen_pendukung' => $documentPath,
                    'status'            => $statusDefault,
                    'approved_by'       => $approvedBy,
                    'approved_at'       => $approvedAt,
                ]);

                // E. Potong Kuota (Jika Auto-Approve & Jenis Cuti Berkuota)
                if ($statusDefault === 'approved') {
                    $this->deductQuota($leaveRequest, $duration);
                }

                // F. Kirim Notifikasi Email (Queue)
                if ($statusDefault === 'pending' && $user->superior) {
                    Mail::to($user->superior->email)->queue(new NewLeaveRequestForSuperior($leaveRequest));
                }

                return redirect()->route('dashboard')->with('success', 'Pengajuan berhasil dikirim.');
            });

        } catch (\Exception $e) {
            // Tangkap error validasi kuota manual atau error DB
            return back()->withErrors(['start_date' => $e->getMessage()])->withInput();
        }
    }

    public function approve(LeaveRequest $leaveRequest)
    {
        $user = Auth::user();

        // Cek: Apakah yang login benar-benar ATASAN dari si pemohon?
        if ($user->id !== $leaveRequest->user->atasan_id) {
            abort(403, 'Akses Ditolak. Anda bukan atasan langsung dari pegawai ini.');
        }

        // Cek: Pastikan statusnya masih pending (supaya tidak diapprove 2x)
        if ($leaveRequest->status !== 'pending') {
            return back()->with('error', 'Pengajuan ini sudah diproses sebelumnya.');
        }

        DB::transaction(function () use ($leaveRequest, $user) {
            $leaveRequest->update([
                'status'      => 'approved',
                'approved_by' => $user->id,
                'approved_at' => now(),
            ]);

            // Hitung durasi dan potong kuota saat diapprove
            $duration = $this->calculateDuration($leaveRequest->start_date, $leaveRequest->end_date);
            $this->deductQuota($leaveRequest, $duration);
        });

        // Email notifikasi
        Mail::to($leaveRequest->user->email)->queue(new LeaveRequestStatusUpdated($leaveRequest));

        return redirect()->route('dashboard')->with('success', 'Pengajuan berhasil disetujui.');
    }

    public function reject(Request $request, LeaveRequest $leaveRequest)
    {
        $user = Auth::user();

        // Cek: Apakah yang login benar-benar ATASAN?
        if ($user->id !== $leaveRequest->user->atasan_id) {
            abort(403, 'Akses Ditolak. Anda bukan atasan langsung dari pegawai ini.');
        }

        // Cek: Status harus pending
        if ($leaveRequest->status !== 'pending') {
            return back()->with('error', 'Pengajuan ini sudah diproses sebelumnya.');
        }

        $validatedData = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $leaveRequest->update([
            'status'           => 'rejected',
            'rejection_reason' => $validatedData['rejection_reason'],
            'approved_by'      => $user->id,
            'approved_at'      => now(),
        ]);

        Mail::to($leaveRequest->user->email)->queue(new LeaveRequestStatusUpdated($leaveRequest));
        
        return redirect()->route('dashboard')->with('success', 'Pengajuan berhasil ditolak.');
    }

    public function print(LeaveRequest $leaveRequest)
    {
        // Otorisasi: Pemilik atau Admin (Gunakan Gate/Role check)
        $isOwner = Auth::id() === $leaveRequest->user_id;
        $isAdmin = Auth::user()->role === 'admin' || Auth::user()->role === 'superadmin' || Auth::user()->role === 'sys_admin';
        // Tambahan: Unit Admin hanya boleh unit sendiri
        if (Auth::user()->role === 'unit_admin' && Auth::user()->unit_kerja_id !== $leaveRequest->user->unit_kerja_id) {
            $isAdmin = false;
        }

        if (!$isOwner && !$isAdmin) {
            abort(403, 'Akses Ditolak');
        }

        // Gunakan view surat perorangan yang sudah dibuat sebelumnya
        // Pastikan path view sesuai (misal: 'admin.leave-requests.print_individual' atau 'leave-requests.pdf')
        $pdf = PDF::loadView('admin.leave-requests.print_individual', ['leaveRequest' => $leaveRequest]);
        return $pdf->stream('surat-izin-'.$leaveRequest->id.'.pdf');
    }
    
    /**
     * Helper: Hitung durasi hari kerja (Excluding Weekend & Holidays)
     */
    private function calculateDuration($start, $end)
    {
        $startDate = Carbon::parse($start);
        $endDate = Carbon::parse($end);
        
        // 1. Ambil Data Libur Nasional dari DB (Range tanggal)
        // Asumsi nama tabel adalah 'holidays' dan kolom tanggal adalah 'date'
        $holidays = DB::table('holidays')
                      ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                      ->pluck('date')
                      ->toArray(); // Array string 'YYYY-MM-DD'

        // 2. Hitung selisih hari
        // Filter: Hanya hitung jika BUKAN Weekend DAN BUKAN Hari Libur Nasional
        $days = $startDate->diffInDaysFiltered(function (Carbon $date) use ($holidays) {
            return !$date->isWeekend() && !in_array($date->format('Y-m-d'), $holidays);
        }, $endDate);

        // Tambah 1 hari karena diffInDaysFiltered itu eksklusif (tidak menghitung hari terakhir)
        // Tapi kita cek dulu apakah hari terakhir itu valid (bukan libur/weekend)
        $lastDayValid = !$endDate->isWeekend() && !in_array($endDate->format('Y-m-d'), $holidays);
        
        return $days + ($lastDayValid ? 1 : 0);
    }

    /**
     * Helper: Potong kuota cuti
     */
    private function deductQuota(LeaveRequest $leaveRequest, $duration = null)
    {
        $leaveType = LeaveType::where('nama_cuti', $leaveRequest->leave_type)->first();

        // Hanya potong jika tipe cuti memiliki kuota (> 0)
        // Izin Sakit (Kuota 0) tidak akan masuk sini
        if ($leaveType && $leaveType->kuota > 0) {
            
            if (is_null($duration)) {
                $duration = $this->calculateDuration($leaveRequest->start_date, $leaveRequest->end_date);
            }

            // Gunakan firstOrCreate agar aman jika record belum ada
            $quota = UserLeaveQuota::firstOrCreate(
                [
                    'user_id'       => $leaveRequest->user_id, 
                    'leave_type_id' => $leaveType->id, 
                    'tahun'         => Carbon::parse($leaveRequest->start_date)->year
                ],
                ['jumlah_diambil' => 0]
            );

            $quota->increment('jumlah_diambil', $duration);
        }
    }
}