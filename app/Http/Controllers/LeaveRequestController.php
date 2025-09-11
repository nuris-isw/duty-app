<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        LeaveRequest::create($validatedData);

        // 4. Redirect ke dashboard dengan pesan sukses
        return redirect()->route('dashboard')->with('success', 'Pengajuan cuti berhasil dikirim.');
    }
}
