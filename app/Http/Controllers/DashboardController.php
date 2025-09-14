<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LeaveRequest;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // --- Inisialisasi variabel untuk dikirim ke view ---
        // Selalu ambil riwayat pengajuan milik user yang login
        $myLeaveRequests = $user->leaveRequests()->orderBy('created_at', 'desc')->get();
        
        // Variabel khusus untuk atasan
        $subordinatePendingRequests = collect(); 
        $subordinateHistoryRequests = collect();

        // Variabel khusus untuk admin
        $stats = [];
        $upcomingLeaves = collect();

        // --- Logika berdasarkan Role ---

        // JIKA user adalah ADMIN, kumpulkan data statistik
        if ($user->role === 'admin') {
            $stats = [
                'total_pegawai' => User::whereIn('role', ['pegawai', 'atasan'])->count(),
                'pending_requests' => LeaveRequest::where('status', 'pending')->count(),
                'approved_this_month' => LeaveRequest::where('status', 'approved')
                                            ->whereMonth('approved_at', Carbon::now()->month)
                                            ->whereYear('approved_at', Carbon::now()->year)
                                            ->count(),
            ];

            // Ambil daftar pegawai yang akan cuti dalam 30 hari ke depan
            $upcomingLeaves = LeaveRequest::with('user')
                                ->where('status', 'approved')
                                ->where('start_date', '>=', Carbon::today())
                                ->where('start_date', '<=', Carbon::today()->addDays(30))
                                ->orderBy('start_date', 'asc')
                                ->get();
        } 
        // JIKA user adalah ATASAN, ambil juga data bawahannya
        else if ($user->role === 'atasan') {
            $subordinateIds = $user->subordinates()->pluck('id');
            
            // Ambil pengajuan yang masih PENDING
            $subordinatePendingRequests = LeaveRequest::with('user')
                                        ->whereIn('user_id', $subordinateIds)
                                        ->where('status', 'pending')
                                        ->orderBy('created_at', 'desc')
                                        ->get();
            
            // Ambil riwayat pengajuan yang sudah di-approve atau di-reject
            $subordinateHistoryRequests = LeaveRequest::with('user')
                                        ->whereIn('user_id', $subordinateIds)
                                        ->whereIn('status', ['approved', 'rejected']) // <-- Kondisi baru
                                        ->orderBy('created_at', 'desc')
                                        ->get();
        }

        // Kirim semua data ke view. Variabel yang tidak relevan akan menjadi koleksi kosong.
        return view('dashboard', [
            'myLeaveRequests' => $myLeaveRequests,
            'subordinatePendingRequests' => $subordinatePendingRequests,
            'subordinateHistoryRequests' => $subordinateHistoryRequests, // <-- Kirim data baru
            'stats' => $stats,
            'upcomingLeaves' => $upcomingLeaves,
        ]);
    }
}
