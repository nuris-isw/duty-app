<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LeaveRequest;
use Carbon\Carbon;

class CalendarController extends Controller
{
    public function index()
    {
        return view('calendar.index');
    }

    public function events()
    {
        // Ambil semua data cuti yang approved (Global / Semua Unit)
        $approvedLeaves = LeaveRequest::with(['user', 'user.jabatan', 'user.unitKerja']) 
            ->where('status', 'approved')
            ->get();

        $events = $approvedLeaves->map(function ($leave) {
            return [
                // JUDUL SINGKAT: Cukup Nama Pegawai saja
                'title' => $leave->user->name, 
                
                'start' => $leave->start_date,
                'end' => Carbon::parse($leave->end_date)->addDay()->toDateString(), 
                
                'backgroundColor' => '#10b981', 
                'borderColor' => '#059669',     
                'textColor' => '#ffffff',
                
                // Info Detail disimpan di sini untuk Tooltip
                'extendedProps' => [
                    'jenis_cuti' => $leave->leave_type,
                    'unit' => $leave->user->unitKerja->nama_unit ?? '-', // Info Unit disini
                    'jabatan' => $leave->user->jabatan->nama_jabatan ?? '-',
                    'reason' => $leave->reason,
                ]
            ];
        });

        return response()->json($events);
    }
}