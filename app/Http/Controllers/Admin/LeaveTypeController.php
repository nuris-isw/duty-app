<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeaveType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LeaveTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $leaveTypes = LeaveType::latest()->get();
        return view('admin.leave-types.index', compact('leaveTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.leave-types.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama_cuti' => 'required|string|max:255|unique:leave_types',
            'kuota' => 'required|integer|min:0',
            'satuan' => 'required|in:hari,kali',
            'periode_reset' => 'required|in:tahunan,tidak_ada',
        ]);

        // Menangani checkbox yang mungkin tidak dikirim jika tidak dicentang
        $validatedData['memerlukan_dokumen'] = $request->has('memerlukan_dokumen');
        $validatedData['bisa_retroaktif'] = $request->has('bisa_retroaktif');

        LeaveType::create($validatedData);

        return redirect()->route('admin.leave-types.index')->with('success', 'Jenis Cuti berhasil ditambahkan.');
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
    public function edit(LeaveType $leaveType)
    {
        return view('admin.leave-types.edit', compact('leaveType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LeaveType $leaveType)
    {
        $validatedData = $request->validate([
            'nama_cuti' => ['required', 'string', 'max:255', Rule::unique('leave_types')->ignore($leaveType->id)],
            'kuota' => 'required|integer|min:0',
            'satuan' => 'required|in:hari,kali',
            'periode_reset' => 'required|in:tahunan,tidak_ada',
        ]);

        $validatedData['memerlukan_dokumen'] = $request->has('memerlukan_dokumen');
        $validatedData['bisa_retroaktif'] = $request->has('bisa_retroaktif');

        $leaveType->update($validatedData);

        return redirect()->route('admin.leave-types.index')->with('success', 'Jenis Cuti berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LeaveType $leaveType)
    {
        $leaveType->delete();
        return redirect()->route('admin.leave-types.index')->with('success', 'Jenis Cuti berhasil dihapus.');
    }
}
