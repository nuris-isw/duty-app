<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AttendanceLog;
use App\Models\LeaveRequest;
use App\Models\Holiday;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Auth;

class MyAttendanceController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        
        $holidays = Holiday::whereBetween('date', [$startDate, $endDate])->get();
        
        // Cek Mapping
        if (!$user->fingerprintUser) {
            return view('my-attendance.unmapped');
        }

        $machineId = $user->fingerprintUser->user_id_machine;

        // 1. Ambil Logs
        $logs = AttendanceLog::where('user_id_machine', $machineId)
            ->whereDate('timestamp', '>=', $startDate)
            ->whereDate('timestamp', '<=', $endDate)
            ->orderBy('timestamp')
            ->get()
            ->groupBy(function($date) {
                return Carbon::parse($date->timestamp)->format('Y-m-d');
            });

        // 2. Ambil Cuti User Ini (Approved)
        $approvedLeaves = LeaveRequest::where('user_id', $user->id)
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

        $attendanceHistory = [];
        $period = CarbonPeriod::create($startDate, $endDate);

        foreach ($period as $dateObj) {
            $dateString = $dateObj->format('Y-m-d');
            $dayLogs = $logs->get($dateString);
            // Cek apakah tanggal ini ada di database libur?
            $isHoliday = $holidays->first(function ($holiday) use ($dateString) {
                return \Carbon\Carbon::parse($holiday->date)->format('Y-m-d') === $dateString;
            });
            // Setup Default
            $clockInTime = '-';
            $clockOutTime = '-';
            $status = 'Mangkir'; 
            $colorClass = 'text-red-600 border-red-200 bg-red-50'; // Default Class Badge

            // --- LOGIKA 1: HARI LIBUR ---
            if ($dateObj->isWeekend() || $isHoliday) {
                // Tentukan Label Libur
                if ($isHoliday) {
                    $status = 'Libur: ' . $isHoliday->title; 
                } else {
                    $status = 'Libur Akhir Pekan';
                }
                $colorClass = 'text-neutral-500 border-neutral-200 bg-neutral-50'; 

                if ($dayLogs && $dayLogs->count() > 0) {
                     $status = 'Lembur / Masuk Libur';
                     $colorClass = 'text-blue-600 border-blue-200 bg-blue-50';
                }
            }
            
            // --- LOGIKA 2: DATA ABSENSI ---
            elseif ($dayLogs && $dayLogs->count() > 0) {
                $morningLogs = $dayLogs->filter(fn($log) => $log->timestamp->hour < 10);
                $afternoonLogs = $dayLogs->filter(fn($log) => $log->timestamp->hour >= 10);

                $inLog = $morningLogs->first();
                $outLog = $afternoonLogs->last();

                if ($inLog) $clockInTime = $inLog->timestamp->format('H:i');
                if ($outLog) $clockOutTime = $outLog->timestamp->format('H:i');

                // --- PENENTUAN STATUS ---
                if ($inLog && $outLog) {
                    // Batas Pulang: Jumat = 11:00, Lainnya = 13:00
                    $batasPulangCepat = $dateObj->isFriday() ? '11:00' : '13:00';

                    $late = $clockInTime > '08:15';
                    $earlyLeave = $clockOutTime < $batasPulangCepat;

                    if ($late && $earlyLeave) {
                        $status = 'Terlambat & Plg Cepat'; // Singkat agar muat di mobile
                        $colorClass = 'text-orange-700 border-orange-200 bg-orange-50';
                    } elseif ($late) {
                        $status = 'Terlambat';
                        $colorClass = 'text-yellow-700 border-yellow-200 bg-yellow-50';
                    } elseif ($earlyLeave) {
                        $status = 'Pulang Cepat';
                        $colorClass = 'text-yellow-700 border-yellow-200 bg-yellow-50';
                    } else {
                        $status = 'Hadir';
                        $colorClass = 'text-green-700 border-green-200 bg-green-50';
                    }

                } elseif ($inLog && !$outLog) {
                    $status = 'Belum Absen Pulang';
                    $colorClass = 'text-orange-700 border-orange-200 bg-orange-50';
                    
                    if ($clockInTime > '08:15') {
                        $status = 'Terlambat & Blm Pulang';
                    }
                } elseif (!$inLog && $outLog) {
                    $status = 'Belum Absen Datang';
                    $colorClass = 'text-orange-700 border-orange-200 bg-orange-50';
                }
            } 
            // --- LOGIKA 3: CEK CUTI ---
            elseif (!$dateObj->isWeekend() && !$isHoliday) {
                $todayLeave = $approvedLeaves->first(function ($leave) use ($dateString) {
                    return $dateString >= $leave->start_date && $dateString <= $leave->end_date;
                });

                if ($todayLeave) {
                    $status = $todayLeave->leave_type;
                    $colorClass = 'text-blue-700 border-blue-200 bg-blue-50';
                } else {
                    $status = 'Mangkir';
                    $colorClass = 'text-red-700 border-red-200 bg-red-50';
                }
            }

            // Masukkan ke array (unshift agar tanggal terbaru di atas)
            array_unshift($attendanceHistory, [
                'date' => $dateString,
                'day_name' => $dateObj->translatedFormat('l'),
                'clock_in' => $clockInTime,
                'clock_out' => $clockOutTime,
                'status' => $status,
                'color_class' => $colorClass,
            ]);
        }

        return view('my-attendance.index', compact('attendanceHistory', 'startDate', 'endDate'));
    }
}