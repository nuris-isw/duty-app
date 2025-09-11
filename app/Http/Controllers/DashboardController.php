<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LeaveRequest;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Selalu ambil riwayat pengajuan milik user yang login
        $myLeaveRequests = $user->leaveRequests()->orderBy('created_at', 'desc')->get();
        
        $subordinatePendingRequests = collect(); // Default koleksi kosong
        $subordinateHistoryRequests = collect(); // Default koleksi kosong untuk riwayat

        // JIKA user adalah atasan, ambil juga data bawahannya
        if ($user->role === 'atasan') {
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

        return view('dashboard', [
            'myLeaveRequests' => $myLeaveRequests,
            'subordinatePendingRequests' => $subordinatePendingRequests,
            'subordinateHistoryRequests' => $subordinateHistoryRequests // <-- Kirim data baru
        ]);
    }
}
