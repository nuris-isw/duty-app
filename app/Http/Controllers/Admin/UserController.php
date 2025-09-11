<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
        // Kirim data atasan ke view
        return view('admin.users.create', ['superiors' => $superiors]);
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
        ]);

        // 2. Buat user baru
        User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']), // Enkripsi password
            'role' => $validatedData['role'],
            'atasan_id' => $validatedData['atasan_id'],
        ]);

        // 3. Redirect dengan pesan sukses
        return redirect()->route('admin.users.index')->with('success', 'User baru berhasil ditambahkan!');
    }
}
