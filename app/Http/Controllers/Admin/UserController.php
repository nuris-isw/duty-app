<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Jabatan;
use App\Models\UnitKerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Menampilkan halaman daftar user.
     */
    public function index()
    {
        // Ambil semua data user dari database
        $users = User::all();
        // Kirim data user ke view
        return view('admin.users.index', ['users' => $users]);
    }

    public function create()
    {
        // Ambil semua user yang rolenya 'atasan' untuk pilihan dropdown
        $superiors = User::where('role', 'atasan')->get(); 
        $unitKerjas = UnitKerja::all();
        $jabatans = Jabatan::all();
        // Kirim data atasan ke view
        return view('admin.users.create', [
            'superiors' => $superiors,
            'unitKerjas' => $unitKerjas,
            'jabatans' => $jabatans,
        ]);
    }

    /**
     * Menyimpan user baru ke database.
     */
    public function store(Request $request)
    {
        // 1. Validasi input
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,atasan,pegawai',
            'atasan_id' => 'nullable|exists:users,id',
            'unit_kerja_id' => 'required|exists:unit_kerjas,id', // <-- Validasi baru
            'jabatan_id' => 'required|exists:jabatans,id', 
        ]);

        // 2. Buat user baru
        User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']), // Enkripsi password
            'role' => $validatedData['role'],
            'atasan_id' => $validatedData['atasan_id'],
            'unit_kerja_id' => $validatedData['unit_kerja_id'], // <-- Simpan data baru
            'jabatan_id' => $validatedData['jabatan_id'], 
        ]);

        // 3. Redirect dengan pesan sukses
        return redirect()->route('admin.users.index')->with('success', 'User baru berhasil ditambahkan!');
    }

    /**
     * Menampilkan form untuk mengedit user.
     */
    public function edit(User $user)
    {
        // Ambil semua data yang dibutuhkan untuk dropdown
        $superiors = User::where('role', 'atasan')->where('id', '!=', $user->id)->get();
        $unitKerjas = UnitKerja::all();
        $jabatans = Jabatan::all();

        return view('admin.users.edit', [
            'user' => $user,
            'superiors' => $superiors,
            'unitKerjas' => $unitKerjas,
            'jabatans' => $jabatans,
        ]);
    }

    /**
     * Memperbarui data user di database.
     */
    public function update(Request $request, User $user)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => 'required|in:admin,atasan,pegawai',
            'unit_kerja_id' => 'required|exists:unit_kerjas,id',
            'jabatan_id' => 'required|exists:jabatans,id',
            'atasan_id' => 'nullable|exists:users,id',
            'password' => 'nullable|string|min:8', // Password tidak wajib diisi saat edit
        ]);

        // Jika ada password baru, enkripsi dan update
        if (!empty($validatedData['password'])) {
            $validatedData['password'] = Hash::make($validatedData['password']);
            $user->update($validatedData);
        } else {
            // Jika tidak ada password baru, update data selain password
            $user->update($request->except('password'));
        }

        return redirect()->route('admin.users.index')->with('success', 'Data user berhasil diperbarui.');
    }

    /**
     * Menghapus user dari database.
     */
    public function destroy(User $user)
    {
        // Tambahkan pengecekan agar admin tidak bisa menghapus akunnya sendiri
        if ($user->id === Auth::id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil dihapus.');
    }
}
