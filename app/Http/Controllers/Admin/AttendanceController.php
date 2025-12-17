<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AttendanceLog;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::today()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::today()->format('Y-m-d'));

        // 1. Ambil User yang punya fingerprint
        $users = User::whereHas('fingerprintUser')->orderBy('name')->get();

        // 2. Ambil Data Cuti yang APPROVED dalam rentang tanggal ini (Pre-fetch)
        $approvedLeaves = LeaveRequest::whereIn('user_id', $users->pluck('id'))
            ->where('status', 'approved')
            ->where(function($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                      ->orWhereBetween('end_date', [$startDate, $endDate])
                      ->orWhere(function($q) use ($startDate, $endDate) {
                          $q->where('start_date', '<', $startDate)
                            ->where('end_date', '>', $endDate);
                      });
            })
            ->get();

        $attendanceReport = [];

        foreach ($users as $user) {
            $machineId = $user->fingerprintUser->user_id_machine;

            // 3. Ambil Logs Raw
            $logs = AttendanceLog::where('user_id_machine', $machineId)
                ->whereDate('timestamp', '>=', $startDate)
                ->whereDate('timestamp', '<=', $endDate)
                ->orderBy('timestamp')
                ->get()
                ->groupBy(function($date) {
                    return Carbon::parse($date->timestamp)->format('Y-m-d');
                });

            $period = CarbonPeriod::create($startDate, $endDate);

            foreach ($period as $dateObj) {
                $dateString = $dateObj->format('Y-m-d');
                $dayLogs = $logs->get($dateString);

                // Default Values
                $clockInTime = '-';
                $clockOutTime = '-';
                $status = 'Mangkir'; 
                $colorClass = 'text-red-600 font-bold';

                // --- LOGIKA 1: CEK HARI LIBUR (SABTU MINGGU) ---
                if ($dateObj->isWeekend()) {
                    $status = 'Libur';
                    $colorClass = 'text-neutral-500 font-bold';
                    
                    if ($dayLogs && $dayLogs->count() > 0) {
                        $status = 'Lembur / Masuk Libur';
                        $colorClass = 'text-blue-600 font-bold';
                    }
                } 
                
                // --- LOGIKA 2: CEK DATA ABSENSI ---
                if ($dayLogs && $dayLogs->count() > 0) {
                    // Pisahkan data: < 10:00 (Pagi/Masuk), >= 10:00 (Siang/Pulang)
                    $morningLogs = $dayLogs->filter(fn($log) => $log->timestamp->hour < 10);
                    $afternoonLogs = $dayLogs->filter(fn($log) => $log->timestamp->hour >= 10);

                    // Ambil Paling Pagi & Paling Akhir
                    $inLog = $morningLogs->first(); 
                    $outLog = $afternoonLogs->last();

                    // Set Jam string
                    if ($inLog) $clockInTime = $inLog->timestamp->format('H:i');
                    if ($outLog) $clockOutTime = $outLog->timestamp->format('H:i');

                    // --- PENENTUAN STATUS ---
                    if ($inLog && $outLog) {
                        // Tentukan batas pulang cepat: Jumat = 11:00, Hari lain = 13:00
                        $batasPulangCepat = $dateObj->isFriday() ? '11:00' : '13:00';
                        // Datang & Pulang ada
                        $late = $clockInTime > '08:15';
                        $earlyLeave = $clockOutTime < $batasPulangCepat; // Logika Pulang Cepat

                        if ($late && $earlyLeave) {
                            $status = 'Terlambat & Pulang Cepat';
                            $colorClass = 'text-orange-600 font-bold';
                        } elseif ($late) {
                            $status = 'Terlambat';
                            $colorClass = 'text-yellow-600 font-bold';
                        } elseif ($earlyLeave) {
                            $status = 'Pulang Cepat';
                            $colorClass = 'text-yellow-600 font-bold';
                        } else {
                            $status = 'Hadir';
                            $colorClass = 'text-green-600 font-bold';
                        }

                    } elseif ($inLog && !$outLog) {
                        // Cuma absen datang
                        $status = 'Belum Absen Pulang';
                        $colorClass = 'text-orange-500 font-bold';
                        
                        if ($clockInTime > '08:15') {
                            $status = 'Terlambat & Belum Pulang';
                        }

                    } elseif (!$inLog && $outLog) {
                        // Cuma absen pulang
                        $status = 'Belum Absen Datang';
                        $colorClass = 'text-orange-500 font-bold';
                    }

                } 
                // --- LOGIKA 3: CEK CUTI (Jika tidak absen & bukan libur) ---
                elseif (!$dateObj->isWeekend()) {
                    $userLeave = $approvedLeaves->first(function ($leave) use ($user, $dateString) {
                        return $leave->user_id == $user->id && 
                               $dateString >= $leave->start_date && 
                               $dateString <= $leave->end_date;
                    });

                    if ($userLeave) {
                        $status = $userLeave->leave_type;
                        $colorClass = 'text-blue-600 font-bold';
                    } else {
                        $status = 'Mangkir';
                        $colorClass = 'text-red-600 font-bold';
                    }
                }

                $attendanceReport[] = [
                    'user_name' => $user->name,
                    'date' => $dateString,
                    'day_name' => $dateObj->translatedFormat('l'),
                    'clock_in' => $clockInTime,
                    'clock_out' => $clockOutTime,
                    'status' => $status,
                    'color_class' => $colorClass,
                ];
            }
        }

        return view('admin.attendance.index', compact('attendanceReport', 'startDate', 'endDate'));
    }
}