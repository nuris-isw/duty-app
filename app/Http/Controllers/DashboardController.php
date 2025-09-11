<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Mengambil user yang sedang login
        $user = Auth::user();

        // Mengambil data pengajuan cuti milik user tersebut
        // Kita gunakan relasi yang akan kita buat nanti: 'leaveRequests'
        $leaveRequests = $user->leaveRequests()->orderBy('created_at', 'desc')->get();

        // Mengirim data ke view dashboard
        return view('dashboard', ['leaveRequests' => $leaveRequests]);
    }
}
