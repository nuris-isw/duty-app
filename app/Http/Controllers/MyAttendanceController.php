<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AttendanceLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class MyAttendanceController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Cek apakah user sudah di-mapping?
        if (!$user->fingerprintUser) {
            return view('my-attendance.unmapped');
        }

        // Filter Tanggal (Default: Bulan ini)
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $machineId = $user->fingerprintUser->user_id_machine;

        // Ambil logs
        $logs = AttendanceLog::where('user_id_machine', $machineId)
            ->whereDate('timestamp', '>=', $startDate)
            ->whereDate('timestamp', '<=', $endDate)
            ->orderBy('timestamp')
            ->get()
            ->groupBy(function($date) {
                return Carbon::parse($date->timestamp)->format('Y-m-d');
            });

        $attendanceHistory = [];
        
        // Loop tanggal dari start sampai end (agar tanggal kosong tetap muncul)
        $period = \Carbon\CarbonPeriod::create($startDate, $endDate);

        foreach ($period as $date) {
            $dateString = $date->format('Y-m-d');
            
            // Skip hari Sabtu/Minggu (Opsional, jika ingin disembunyikan)
            // if ($date->isWeekend()) continue;

            $dayLogs = $logs->get($dateString);

            $data = [
                'date' => $dateString,
                'day_name' => $date->translatedFormat('l'),
                'clock_in' => '-',
                'clock_out' => '-',
                'status' => 'Tidak Hadir/Libur',
                'color_class' => 'text-neutral-400',
            ];

            if ($dayLogs && $dayLogs->count() > 0) {
                $firstLog = $dayLogs->first();
                $lastLog = $dayLogs->last();

                $clockIn = Carbon::parse($firstLog->timestamp);
                $clockOut = Carbon::parse($lastLog->timestamp);

                $data['clock_in'] = $clockIn->format('H:i');
                $data['clock_out'] = ($dayLogs->count() > 1) ? $clockOut->format('H:i') : '-';

                // Logika Terlambat (Contoh: Masuk > 08:00)
                $officeStartTime = Carbon::parse($dateString . ' 08:15:00');

                if ($clockIn->gt($officeStartTime)) {
                    $data['status'] = 'Terlambat';
                    $data['color_class'] = 'text-yellow-600 font-bold';
                } else {
                    $data['status'] = 'Hadir';
                    $data['color_class'] = 'text-green-600 font-bold';
                }
            }

            // Urutkan dari tanggal terbaru ke terlama (biar yang hari ini paling atas)
            array_unshift($attendanceHistory, $data);
        }

        return view('my-attendance.index', compact('attendanceHistory', 'startDate', 'endDate'));
    }
}