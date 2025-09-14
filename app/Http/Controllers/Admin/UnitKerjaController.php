<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UnitKerja;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UnitKerjaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $unitKerjas = UnitKerja::latest()->get(); // Ambil semua data, urutkan dari yang terbaru
        return view('admin.unit-kerja.index', compact('unitKerjas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.unit-kerja.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi data yang masuk
        $validatedData = $request->validate([
            'nama_unit' => 'required|string|max:255|unique:unit_kerjas',
            'lokasi' => 'nullable|string|max:255',
        ]);

        // Simpan ke database
        UnitKerja::create($validatedData);

        // Redirect ke halaman index dengan pesan sukses
        return redirect()->route('admin.unit-kerja.index')->with('success', 'Unit Kerja berhasil ditambahkan.');
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
    public function edit(UnitKerja $unitKerja)
    {
        return view('admin.unit-kerja.edit', compact('unitKerja'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, UnitKerja $unitKerja)
    {
        $validatedData = $request->validate([
            'nama_unit' => ['required', 'string', 'max:255', Rule::unique('unit_kerjas')->ignore($unitKerja->id)],
            'lokasi' => 'nullable|string|max:255',
        ]);

        $unitKerja->update($validatedData);

        return redirect()->route('admin.unit-kerja.index')->with('success', 'Unit Kerja berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UnitKerja $unitKerja)
    {
        $unitKerja->delete();
        return redirect()->route('admin.unit-kerja.index')->with('success', 'Unit Kerja berhasil dihapus.');
    }
}
