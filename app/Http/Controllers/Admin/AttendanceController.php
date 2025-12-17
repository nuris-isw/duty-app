<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\FingerDeviceUser;
use App\Models\AttendanceLog;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil Filter Tanggal (Default: Hari ini)
        $startDate = $request->input('start_date', Carbon::today()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::today()->format('Y-m-d'));

        // 2. Ambil semua pegawai yang sudah di-mapping
        // Kita gunakan 'whereHas' untuk mengambil user yang punya data fingerprint
        $users = User::whereHas('fingerprintUser')->orderBy('name')->get();

        $attendanceReport = [];

        // 3. LOGIKA UTAMA: Loop setiap user untuk hitung absensi
        foreach ($users as $user) {
            // Ambil ID Mesin milik user ini
            $machineId = $user->fingerprintUser->user_id_machine;

            // Ambil logs user ini dalam rentang tanggal
            $logs = AttendanceLog::where('user_id_machine', $machineId)
                ->whereDate('timestamp', '>=', $startDate)
                ->whereDate('timestamp', '<=', $endDate)
                ->orderBy('timestamp')
                ->get()
                ->groupBy(function($date) {
                    return Carbon::parse($date->timestamp)->format('Y-m-d');
                });

            // Loop per tanggal dalam rentang filter (untuk handle hari kosong)
            $period = \Carbon\CarbonPeriod::create($startDate, $endDate);

            foreach ($period as $date) {
                $dateString = $date->format('Y-m-d');
                $dayLogs = $logs->get($dateString);

                // Default data (Alpa/Libur)
                $data = [
                    'user_name' => $user->name,
                    'date' => $dateString,
                    'day_name' => $date->translatedFormat('l'),
                    'clock_in' => '-',
                    'clock_out' => '-',
                    'status' => 'Tidak Hadir', // Bisa diset Alpha/Libur
                    'color_class' => 'text-red-600',
                ];

                if ($dayLogs && $dayLogs->count() > 0) {
                    // AMBIL JAM MASUK (Scan Pertama) & JAM PULANG (Scan Terakhir)
                    $firstLog = $dayLogs->first();
                    $lastLog = $dayLogs->last();

                    $clockIn = Carbon::parse($firstLog->timestamp);
                    $clockOut = Carbon::parse($lastLog->timestamp);

                    $data['clock_in'] = $clockIn->format('H:i');
                    
                    // Jika scan cuma 1 kali, jam pulang dianggap sama dengan jam masuk
                    $data['clock_out'] = ($dayLogs->count() > 1) ? $clockOut->format('H:i') : '-';

                    // LOGIKA STATUS KEHADIRAN
                    // Contoh: Jam masuk kantor 08:00
                    $officeStartTime = Carbon::parse($dateString . ' 08:15:00');

                    if ($clockIn->gt($officeStartTime)) {
                        $data['status'] = 'Terlambat';
                        $data['color_class'] = 'text-yellow-600 font-bold';
                    } else {
                        $data['status'] = 'Hadir';
                        $data['color_class'] = 'text-green-600 font-bold';
                    }
                }

                $attendanceReport[] = $data;
            }
        }

        return view('admin.attendance.index', compact('attendanceReport', 'startDate', 'endDate'));
    }
}