<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\LeaveRequest;
use App\Models\LeaveType; // <--- Jangan lupa import ini
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $today = Carbon::today();
        
        // --- 1. DATA PRIBADI ---
        $myLeaveRequests = LeaveRequest::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // --- 2. DATA ADMINISTRATOR ---
        $stats = ['total_pegawai' => 0, 'pending_requests' => 0, 'approved_this_month' => 0];
        $upcomingLeaves = collect();
        $employeeBalances = collect(); // <--- Variabel Baru

        if (Gate::allows('access-admin-panel')) {
            
            $queryUser = User::query();
            $queryLeave = LeaveRequest::query();

            // --- DATA SCOPING ---
            if (Gate::allows('view-all-units')) {
                // Superadmin & SysAdmin
                if ($user->role === 'sys_admin') {
                    $queryUser->where('role', '!=', 'superadmin');
                    $queryLeave->whereHas('user', fn($q) => $q->where('role', '!=', 'superadmin'));
                }
            } else {
                // Unit Admin
                $queryUser->where('unit_kerja_id', $user->unit_kerja_id);
                $queryLeave->whereHas('user', fn($q) => $q->where('unit_kerja_id', $user->unit_kerja_id));
            }

            // Hitung Statistik Dasar
            $stats['total_pegawai'] = $queryUser->count();
            
            $pendingQuery = clone $queryLeave;
            $stats['pending_requests'] = $pendingQuery->where('status', 'pending')->count();

            $approvedMonthQuery = clone $queryLeave;
            $stats['approved_this_month'] = $approvedMonthQuery
                ->where('status', 'approved')
                ->whereMonth('start_date', Carbon::now()->month)
                ->whereYear('start_date', Carbon::now()->year)
                ->count();

            // Data Jadwal Cuti
            $upcomingQuery = clone $queryLeave;
            $upcomingLeaves = $upcomingQuery
                ->with(['user', 'user.jabatan', 'user.unitKerja'])
                ->where('status', 'approved')
                ->where('start_date', '>', $today)
                ->where('start_date', '<=', Carbon::now()->addDays(30))
                ->orderBy('start_date', 'asc')
                ->limit(10)
                ->get();

            // --- LOGIKA BARU: SISA CUTI PEGAWAI ---
            // 1. Ambil Setting Cuti Tahunan (Asumsi nama_cuti = 'Cuti Tahunan')
            $annualLeaveType = LeaveType::where('nama_cuti', 'Cuti Tahunan')->first();
            $defaultQuota = $annualLeaveType ? $annualLeaveType->kuota : 12; // Default 12 jika tidak ada di DB
            $currentYear = Carbon::now()->year;

            // 2. Query User beserta data kuota tahun ini
            $employeeBalances = $queryUser->with(['unitKerja', 'jabatan', 'userLeaveQuotas' => function($q) use ($annualLeaveType, $currentYear) {
                if($annualLeaveType) {
                    $q->where('leave_type_id', $annualLeaveType->id)
                      ->where('tahun', $currentYear);
                }
            }])
            ->get()
            ->map(function($emp) use ($defaultQuota) {
                // Hitung sisa cuti di controller agar view bersih
                $used = $emp->userLeaveQuotas->first()->jumlah_diambil ?? 0;
                $emp->sisa_cuti = max(0, $defaultQuota - $used);
                $emp->kuota_terpakai = $used;
                return $emp;
            })
            // LOGIKA BARU: Sortir Koleksi
            ->sortBy(function($emp) {
                // Urutkan berdasarkan: Nama Unit (A-Z) lalu Nama Pegawai (A-Z)
                // Jika unit kosong, taruh di paling bawah ('ZZZ')
                $unitName = $emp->unitKerja->nama_unit ?? 'ZZZ';
                return $unitName . '#' . $emp->name;
            });
        }

        // --- 3. DATA ATASAN ---
        $subordinatePendingRequests = collect();
        $subordinateHistoryRequests = collect();
        
        if (User::where('atasan_id', $user->id)->exists()) {
            $subordinatePendingRequests = LeaveRequest::with('user')
                ->whereHas('user', fn($q) => $q->where('atasan_id', $user->id))
                ->where('status', 'pending')
                ->orderBy('created_at', 'asc')->get();

            $subordinateHistoryRequests = LeaveRequest::with('user')
                ->whereHas('user', fn($q) => $q->where('atasan_id', $user->id))
                ->whereIn('status', ['approved', 'rejected'])
                ->orderBy('updated_at', 'desc')->limit(5)->get();
        }

        // --- 4. CUTI HARI INI ---
        $currentlyOnLeave = collect();
        if ($user->unit_kerja_id) {
            $currentlyOnLeave = LeaveRequest::with('user')
                ->where('status', 'approved')
                ->whereDate('start_date', '<=', $today)
                ->whereDate('end_date', '>=', $today)
                ->whereHas('user', function ($query) use ($user) {
                    $query->where('unit_kerja_id', $user->unit_kerja_id)->where('id', '!=', $user->id);
                })->get();
        }

        return view('dashboard', compact(
            'stats', 'myLeaveRequests', 'currentlyOnLeave', 'upcomingLeaves',
            'subordinatePendingRequests', 'subordinateHistoryRequests', 'employeeBalances' // <--- Tambah ini
        ));
    }
}