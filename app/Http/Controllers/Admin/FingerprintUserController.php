<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FingerDeviceUser;
use App\Models\User;
use Illuminate\Http\Request;

class FingerprintUserController extends Controller
{
    /**
     * Menampilkan daftar user dari mesin untuk di-mapping.
     */
    public function index()
    {
        // Ambil data user mesin, urutkan yang belum di-mapping (user_id null) di atas
        $fingerUsers = FingerDeviceUser::with('user')
            ->orderByRaw('user_id IS NULL DESC') // Prioritaskan yang belum ada pasangannya
            ->orderBy('name')
            ->get();

        // Ambil list pegawai aktif untuk dropdown
        $users = User::orderBy('name')->get();

        return view('admin.attendance.mapping', compact('fingerUsers', 'users'));
    }

    /**
     * Menyimpan perubahan mapping (Sync).
     */
    public function update(Request $request, $id)
    {
        $fingerUser = FingerDeviceUser::findOrFail($id);
        
        // Validasi input
        $request->validate([
            'user_id' => 'nullable|exists:users,id',
        ]);

        // Cek apakah user_id ini sudah dipakai oleh ID Mesin lain? (Opsional, untuk mencegah duplikat)
        if ($request->user_id) {
            $exists = FingerDeviceUser::where('user_id', $request->user_id)
                ->where('id', '!=', $id)
                ->exists();
                
            if ($exists) {
                return back()->with('error', 'Pegawai tersebut sudah dipasangkan dengan ID Mesin lain.');
            }
        }

        // Simpan
        $fingerUser->update([
            'user_id' => $request->user_id
        ]);

        return back()->with('success', 'Mapping berhasil diperbarui.');
    }
}