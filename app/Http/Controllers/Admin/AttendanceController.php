<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AttendanceLog;
use App\Models\LeaveRequest;
use App\Models\Holiday;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::today()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::today()->format('Y-m-d'));
        $selectedUserId = $request->input('user_id');
        $selectedStatus = $request->input('status');

        $allUsers = User::whereHas('fingerprintUser')->orderBy('name')->get();

        $usersQuery = User::whereHas('fingerprintUser')->orderBy('name');
        if ($selectedUserId) {
            $usersQuery->where('id', $selectedUserId);
        }
        $users = $usersQuery->get();

        // --- AMBIL DATA HARI LIBUR ---
        $holidays = Holiday::whereBetween('date', [$startDate, $endDate])->get();

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
        
        // --- INISIALISASI SUMMARY ---
        $summary = [
            'hadir' => 0,        // Total orang yang datang (termasuk lembur/telat)
            'terlambat' => 0,    
            'pulang_cepat' => 0, 
            'belum_datang' => 0,
            'belum_pulang' => 0,
            'mangkir' => 0,
            'total_cuti' => 0,   
            'total_sakit' => 0,   
        ];

        foreach ($users as $user) {
            $machineId = $user->fingerprintUser->user_id_machine;

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

                // --- PERBAIKAN: CARA CEK HOLIDAY ---
                // Bandingkan format string Y-m-d agar akurat
                $isHoliday = $holidays->first(function ($holiday) use ($dateString) {
                    return $holiday->date->format('Y-m-d') === $dateString;
                });

                $clockInTime = '-';
                $clockOutTime = '-';
                $status = 'Mangkir'; 
                $colorClass = 'text-red-600 font-bold';

                // LOGIKA 1: HARI LIBUR (Weekend atau Tanggal Merah)
                if ($dateObj->isWeekend() || $isHoliday) {
                    // Tentukan Label Libur
                    if ($isHoliday) {
                        $status = 'Libur: ' . $isHoliday->title; 
                    } else {
                        $status = 'Libur Akhir Pekan';
                    }
                    $colorClass = 'text-neutral-500 font-bold';
                    
                    // Jika ada absen masuk saat libur -> Lembur
                    if ($dayLogs && $dayLogs->count() > 0) {
                        $status = 'Lembur / Masuk Libur';
                        $colorClass = 'text-blue-600 font-bold';
                        
                        // Tambahkan ke summary Hadir karena dia masuk
                        $summary['hadir']++;
                        
                        // Opsional: Tampilkan jam masuknya juga saat lembur
                        $inLog = $dayLogs->first();
                        $outLog = $dayLogs->last();
                        if ($inLog) $clockInTime = $inLog->timestamp->format('H:i');
                        if ($outLog && $dayLogs->count() > 1) $clockOutTime = $outLog->timestamp->format('H:i');
                    }
                } 
                
                // LOGIKA 2: DATA ABSENSI (HARI KERJA BIASA)
                // Gunakan elseif agar tidak menimpa logika libur (kecuali ada log di atas)
                elseif ($dayLogs && $dayLogs->count() > 0) {
                    $summary['hadir']++; // Hitung Hadir

                    $morningLogs = $dayLogs->filter(fn($log) => $log->timestamp->hour < 10);
                    $afternoonLogs = $dayLogs->filter(fn($log) => $log->timestamp->hour >= 10);

                    $inLog = $morningLogs->first(); 
                    $outLog = $afternoonLogs->last();

                    if ($inLog) $clockInTime = $inLog->timestamp->format('H:i');
                    if ($outLog) $clockOutTime = $outLog->timestamp->format('H:i');

                    if ($inLog && $outLog) {
                        $batasPulangCepat = $dateObj->isFriday() ? '11:00' : '13:00';
                        $late = $clockInTime > '08:15';
                        $earlyLeave = $clockOutTime < $batasPulangCepat;

                        if ($late && $earlyLeave) {
                            $status = 'Terlambat & Pulang Cepat';
                            $colorClass = 'text-orange-600 font-bold';
                            $summary['terlambat']++;
                            $summary['pulang_cepat']++;
                        } elseif ($late) {
                            $status = 'Terlambat';
                            $colorClass = 'text-yellow-600 font-bold';
                            $summary['terlambat']++;
                        } elseif ($earlyLeave) {
                            $status = 'Pulang Cepat';
                            $colorClass = 'text-yellow-600 font-bold';
                            $summary['pulang_cepat']++;
                        } else {
                            $status = 'Hadir';
                            $colorClass = 'text-green-600 font-bold';
                        }

                    } elseif ($inLog && !$outLog) {
                        $status = 'Belum Absen Pulang';
                        $colorClass = 'text-orange-500 font-bold';
                        $summary['belum_pulang']++;
                        
                        if ($clockInTime > '08:15') {
                            $status = 'Terlambat & Belum Pulang';
                            $summary['terlambat']++;
                        }

                    } elseif (!$inLog && $outLog) {
                        $status = 'Belum Absen Datang';
                        $colorClass = 'text-orange-500 font-bold';
                        $summary['belum_datang']++;
                    }

                } 
                // LOGIKA 3: CEK CUTI (Jika tidak absen & bukan libur)
                else {
                    $userLeave = $approvedLeaves->first(function ($leave) use ($user, $dateString) {
                        return $leave->user_id == $user->id && 
                               $dateString >= $leave->start_date && 
                               $dateString <= $leave->end_date;
                    });

                    if ($userLeave) {
                        $status = $userLeave->leave_type;
                        $colorClass = 'text-blue-600 font-bold';
                        
                        if (stripos($status, 'sakit') !== false) {
                            $summary['total_sakit']++;
                        } else {
                            $summary['total_cuti']++;
                        }

                    } else {
                        $status = 'Mangkir';
                        $colorClass = 'text-red-600 font-bold';
                        $summary['mangkir']++;
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

        // --- FILTER FINAL ARRAY (LOGIKA BARU) ---
        if ($selectedStatus) {
            $attendanceReport = collect($attendanceReport)->filter(function ($row) use ($selectedStatus) {
                // Gunakan stripos agar pencarian tidak case-sensitive dan parsial
                // Contoh: Status "Terlambat & Pulang Cepat" akan muncul jika filter "Terlambat" DIPILIH
                return stripos($row['status'], $selectedStatus) !== false;
            })->values()->all();
        }

        return view('admin.attendance.index', compact(
            'attendanceReport', 
            'startDate', 
            'endDate', 
            'allUsers', 
            'selectedUserId', 
            'summary',
            'selectedStatus'
        ));
    }
}