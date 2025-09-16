<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // Ambil data untuk dropdown filter
        $users = User::whereIn('role', ['pegawai', 'atasan'])->orderBy('name')->get();
        $leaveTypes = LeaveType::orderBy('nama_cuti')->get();

        // Jalankan query filter
        $leaveRequests = $this->getFilteredLeaveRequests($request);

        return view('admin.reports.index', [
            'leaveRequests' => $leaveRequests,
            'users' => $users,
            'leaveTypes' => $leaveTypes,
            'filters' => $request->all(), // Kirim semua input filter ke view
        ]);
    }

    public function print(Request $request)
    {
        // Jalankan query filter yang sama
        $leaveRequests = $this->getFilteredLeaveRequests($request);
        
        // Ambil tanggal dari request untuk dikirim ke view
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $data = [
            'leaveRequests' => $leaveRequests,
            'startDate' => $startDate, // <-- Kirim variabel ini
            'endDate' => $endDate,     // <-- Kirim variabel ini
        ];

        $pdf = PDF::loadView('admin.reports.pdf', $data);
        return $pdf->stream('laporan-cuti.pdf');
    }

    /**
     * Method privat untuk menjalankan logika query yang sama
     */
    private function getFilteredLeaveRequests(Request $request)
    {
        $query = LeaveRequest::with(['user', 'user.jabatan']);

        // Filter berdasarkan tanggal
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('start_date', [$request->start_date, $request->end_date]);
        }
        // Filter berdasarkan user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        // Filter berdasarkan jenis cuti
        if ($request->filled('leave_type')) {
            $query->where('leave_type', $request->leave_type);
        }
        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', 'like', '%' . $request->status . '%');
        }

        return $query->orderBy('start_date', 'desc')->get();
    }
}
