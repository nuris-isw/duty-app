<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jabatan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class JabatanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $jabatans = Jabatan::latest()->get(); // Ambil semua data, urutkan dari yang terbaru
        return view('admin.jabatan.index', compact('jabatans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.jabatan.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama_jabatan' => ['required', 'string', 'max:255', Rule::unique('jabatans')->where('bidang_kerja', $request->bidang_kerja)->ignore($jabatan->id)],
            'alias' => 'nullable|string|max:255',
            'bidang_kerja' => 'nullable|string|max:255',
        ]);

        Jabatan::create($validatedData);
        return redirect()->route('admin.jabatan.index')->with('success', 'Jabatan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Jabatan $jabatan)
    {
        return view('admin.jabatan.edit', compact('jabatan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Jabatan $jabatan)
    {
        $validatedData = $request->validate([
            'nama_jabatan' => ['required', 'string', 'max:255', Rule::unique('jabatans')->where('bidang_kerja', $request->bidang_kerja)->ignore($jabatan->id)],
            'alias' => 'nullable|string|max:255',
            'bidang_kerja' => 'nullable|string|max:255',
        ]);

        $jabatan->update($validatedData);
        return redirect()->route('admin.jabatan.index')->with('success', 'Jabatan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Jabatan $jabatan)
    {
        $jabatan->delete();
        return redirect()->route('admin.jabatan.index')->with('success', 'Jabatan berhasil dihapus.');
    }
}
