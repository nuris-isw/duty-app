<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LeaveRequest;

class CalendarController extends Controller
{
    // Method untuk menampilkan halaman kalender
    public function index()
    {
        return view('calendar.index');
    }

    // Method API untuk mengirim data cuti yang sudah disetujui
    public function events()
    {
        $approvedLeaves = LeaveRequest::with('user')
            ->where('status', 'approved')
            ->get();

        $events = $approvedLeaves->map(function ($leave) {
            return [
                'title' => $leave->user->name,
                'start' => $leave->start_date,
                'end' => \Carbon\Carbon::parse($leave->end_date)->addDay()->toDateString(), // Tambah 1 hari agar event mencakup tanggal akhir
                'backgroundColor' => '#28a745',
                'borderColor' => '#28a745'
            ];
        });

        return response()->json($events);
    }
}
