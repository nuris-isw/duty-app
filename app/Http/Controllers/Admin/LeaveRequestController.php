<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class LeaveRequestController extends Controller
{
    /**
     * Menampilkan form edit pengajuan cuti.
     */
    public function edit(LeaveRequest $leaveRequest)
    {
        // 1. Cek Hak Akses (Security Check)
        // Memastikan admin yang login berhak mengedit data ini
        $this->authorizeAction($leaveRequest);

        // 2. Ambil data jenis cuti untuk dropdown di form
        $leaveTypes = LeaveType::all();

        // 3. Tampilkan view edit
        return view('admin.leave-requests.edit', compact('leaveRequest', 'leaveTypes'));
    }

    /**
     * Update data pengajuan cuti ke database.
     */
    public function update(Request $request, LeaveRequest $leaveRequest)
    {
        // 1. Cek Hak Akses
        $this->authorizeAction($leaveRequest);

        // 2. Validasi Input
        $validatedData = $request->validate([
            'leave_type' => 'required|exists:leave_types,nama_cuti', // Pastikan jenis cuti valid
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'reason'     => 'required|string|max:255',
            'status'     => 'required|in:pending,approved,rejected',
        ]);

        // 3. Update Data
        $leaveRequest->update($validatedData);

        // 4. Redirect kembali ke halaman laporan dengan pesan sukses
        return redirect()->route('admin.reports.index')->with('success', 'Data pengajuan berhasil diperbarui.');
    }

    /**
     * Hapus data pengajuan cuti secara permanen.
     */
    public function destroy(LeaveRequest $leaveRequest)
    {
        // 1. Cek Hak Akses
        $this->authorizeAction($leaveRequest);

        // 2. Hapus Data
        $leaveRequest->delete();

        // 3. Redirect kembali ke halaman laporan dengan pesan sukses
        return redirect()->route('admin.reports.index')->with('success', 'Data pengajuan berhasil dihapus.');
    }

    /**
     * Private Method: Logika Proteksi Data (RBAC & Scoping)
     * -----------------------------------------------------
     * Method ini dipanggil di setiap fungsi (edit, update, destroy)
     * untuk memvalidasi apakah admin yang sedang login berhak
     * melakukan tindakan terhadap data cuti tertentu.
     */
    private function authorizeAction(LeaveRequest $leaveRequest)
    {
        $user = Auth::user();

        // SCENARIO 1: Superadmin
        // Boleh melakukan apa saja terhadap data siapapun.
        if ($user->role === 'superadmin') {
            return true;
        }

        // SCENARIO 2: SysAdmin
        // Boleh mengelola data semua orang, KECUALI data milik Superadmin.
        if ($user->role === 'sys_admin') {
            if ($leaveRequest->user->role === 'superadmin') {
                abort(403, 'Anda tidak memiliki akses untuk mengubah data Superadmin.');
            }
            return true;
        }

        // SCENARIO 3: Unit Admin
        // Hanya boleh mengelola data pegawai yang berada di UNIT KERJA YANG SAMA.
        if ($user->role === 'unit_admin') {
            // Cek kesamaan ID Unit Kerja
            if ($leaveRequest->user->unit_kerja_id !== $user->unit_kerja_id) {
                abort(403, 'Anda hanya dapat mengelola data pegawai di unit kerja Anda sendiri.');
            }
            return true;
        }

        // Default: Jika role tidak dikenali atau user biasa mencoba akses
        abort(403, 'Akses ditolak.');
    }

    /**
     * Cetak PDF Pengajuan Cuti
     */
    public function print(LeaveRequest $leaveRequest)
    {
        $this->authorizeAction($leaveRequest); 

        // GANTI VIEW KE VIEW YANG BARU KITA BUAT
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.leave-requests.print_individual', [
            'leaveRequest' => $leaveRequest,
        ]);
        
        $fileName = 'SURAT_IZIN_' . strtoupper(str_replace(' ', '_', $leaveRequest->user->name)) . '.pdf';
        return $pdf->stream($fileName);
    }
}