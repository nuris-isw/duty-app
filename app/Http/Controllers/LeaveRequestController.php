<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail; // <-- Tambahkan ini
use App\Mail\LeaveRequestStatusUpdated; // <-- Tambahkan ini
use App\Mail\NewLeaveRequestForSuperior; // <-- Tambahkan ini
use Barryvdh\DomPDF\Facade\Pdf; 

class LeaveRequestController extends Controller
{
    public function create()
    {
        // Daftar jenis cuti yang bisa dipilih
        $leaveTypes = [
            'Cuti Tahunan',
            'Izin Sakit',
            'Cuti Alasan Penting',
            'Cuti Melahirkan',
        ];

        return view('leave-requests.create', ['leaveTypes' => $leaveTypes]);
    }

    public function store(Request $request)
    {
        // 1. Validasi data form
        $validatedData = $request->validate([
            'leave_type' => 'required|string',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string',
        ]);

        // 2. Tambahkan user_id dari user yang sedang login
        $validatedData['user_id'] = Auth::id();

        // 3. Simpan ke database
        $leaveRequest = LeaveRequest::create($validatedData);

        // 4. KIRIM EMAIL KE ATASAN (Logika Baru)
        $user = Auth::user();
        // Cek apakah user punya atasan
        if ($user->atasan_id) {
            // Ambil data atasan
            $superior = $user->superior; 
            // Kirim email ke atasan
            Mail::to($superior->email)->send(new NewLeaveRequestForSuperior($leaveRequest));
        }

        // 4. Redirect ke dashboard dengan pesan sukses
        return redirect()->route('dashboard')->with('success', 'Pengajuan cuti berhasil dikirim.');
    }

    /**
     * Menyetujui pengajuan cuti.
     */
    public function approve(LeaveRequest $leaveRequest)
    {
        // Otorisasi: Pastikan user yang login adalah atasan dari pemohon
        // (Ini akan kita tambahkan nanti untuk keamanan ekstra)

        $leaveRequest->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        // Kirim email ke pegawai yang mengajukan
        Mail::to($leaveRequest->user->email)->send(new LeaveRequestStatusUpdated($leaveRequest));

        return redirect()->route('dashboard')->with('success', 'Pengajuan berhasil disetujui.');
    }

    /**
     * Menolak pengajuan cuti.
     */
    public function reject(LeaveRequest $leaveRequest)
    {
        // Otorisasi: Sama seperti di atas
        
        $leaveRequest->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        // Kirim email ke pegawai yang mengajukan
        Mail::to($leaveRequest->user->email)->send(new LeaveRequestStatusUpdated($leaveRequest));

        return redirect()->route('dashboard')->with('success', 'Pengajuan berhasil ditolak.');
    }

    /**
     * Menyiapkan data dan men-generate PDF.
     */
    public function print(LeaveRequest $leaveRequest)
    {
        // 1. Otorisasi (pastikan user yang meminta adalah pemilik atau admin)
        if (Auth::id() !== $leaveRequest->user_id && Auth::user()->role !== 'admin') {
            abort(403, 'Akses Ditolak');
        }

        // 2. Langsung load view dan kirim objek $leaveRequest
        // Relasi 'user' (pemohon) dan 'approver' (atasan) akan diakses dari dalam view
        $pdf = PDF::loadView('leave-requests.pdf', ['leaveRequest' => $leaveRequest]);

        // 3. Tampilkan PDF di browser
        return $pdf->stream('surat-izin-'.$leaveRequest->id.'.pdf');
    }
}
