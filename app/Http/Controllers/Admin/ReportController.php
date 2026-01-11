<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil data User untuk Dropdown Filter (Terapkan Data Scoping)
        $usersQuery = User::orderBy('name');

        if (Gate::allows('view-all-units')) {
            // Superadmin & SysAdmin: Bisa lihat semua user
            // Optional: SysAdmin tidak perlu lihat Superadmin di dropdown
            if (Auth::user()->role === 'sys_admin') {
                $usersQuery->where('role', '!=', 'superadmin');
            }
        } else {
            // Unit Admin: Hanya melihat user di unit kerja yang sama
            $usersQuery->where('unit_kerja_id', Auth::user()->unit_kerja_id);
        }

        $users = $usersQuery->get();
        $leaveTypes = LeaveType::orderBy('nama_cuti')->get();

        // 2. Jalankan query filter Laporan
        $leaveRequests = $this->getFilteredLeaveRequests($request);

        return view('admin.reports.index', [
            'leaveRequests' => $leaveRequests,
            'users' => $users,
            'leaveTypes' => $leaveTypes,
            'filters' => $request->all(),
        ]);
    }

    public function print(Request $request)
    {
        // Jalankan query filter yang sama
        $leaveRequests = $this->getFilteredLeaveRequests($request);
        
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $data = [
            'leaveRequests' => $leaveRequests,
            'startDate' => $startDate, 
            'endDate' => $endDate,     
        ];

        $pdf = PDF::loadView('admin.reports.pdf', $data);
        return $pdf->stream('laporan-cuti.pdf');
    }

    /**
     * Method privat untuk menjalankan logika query + Data Scoping
     */
    private function getFilteredLeaveRequests(Request $request)
    {
        $currentUser = Auth::user();
        $query = LeaveRequest::with(['user', 'user.jabatan']);

        // --- DATA SCOPING (RBAC) ---
        if (Gate::allows('view-all-units')) {
            // SCENARIO 1: Superadmin & SysAdmin (Lihat Semua)
            
            // Proteksi: SysAdmin tidak boleh melihat cuti milik Superadmin (Opsional, tapi disarankan)
            if ($currentUser->role === 'sys_admin') {
                $query->whereHas('user', function($q) {
                    $q->where('role', '!=', 'superadmin');
                });
            }
        } 
        elseif ($currentUser->role === 'unit_admin') {
            // SCENARIO 2: Unit Admin (Hanya Unit Sendiri)
            $query->whereHas('user', function($q) use ($currentUser) {
                $q->where('unit_kerja_id', $currentUser->unit_kerja_id);
            });
        } 
        else {
            // SCENARIO 3: User Biasa (Hanya Data Sendiri - Jika akses controller ini)
            $query->where('user_id', $currentUser->id);
        }
        // ---------------------------

        // Filter standard
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('start_date', [$request->start_date, $request->end_date]);
        }
        
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        
        if ($request->filled('leave_type')) {
            $query->where('leave_type', $request->leave_type);
        }
        
        if ($request->filled('status')) {
            $query->where('status', 'like', '%' . $request->status . '%');
        }

        return $query->orderBy('start_date', 'desc')->get();
    }
}