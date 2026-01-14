<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AttendanceLog;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Models\Holiday;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class AttendanceController extends Controller
{
    /**
     * Menampilkan Data Absensi (Hanya bersumber dari tabel 'attendances')
     */
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::today()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::today()->format('Y-m-d'));
        $selectedUserId = $request->input('user_id');
        $selectedStatus = $request->input('status');

        // 1. Ambil User
        $usersQuery = User::whereHas('fingerprintUser')->orderBy('name');
        if ($selectedUserId) {
            $usersQuery->where('id', $selectedUserId);
        }
        $users = $usersQuery->get();
        // Variabel $allUsers untuk dropdown filter
        $allUsers = User::whereHas('fingerprintUser')->orderBy('name')->get(); 

        // 2. Ambil Data Matang (Attendance) secara Eager Loading
        // Kita ambil semua data pada rentang tanggal ini sekaligus agar hemat query
        $attendancesDB = Attendance::whereIn('user_id', $users->pluck('id'))
            ->whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $endDate)
            ->get()
            ->groupBy('user_id'); // Grouping by User ID biar gampang dicari di loop

        $attendanceReport = [];
        $period = CarbonPeriod::create($startDate, $endDate);

        // 3. Inisialisasi Summary
        $summary = [
            'hadir' => 0, 'terlambat' => 0, 'pulang_cepat' => 0, 
            'belum_datang' => 0, 'belum_pulang' => 0, 'mangkir' => 0,
            'total_cuti' => 0, 'total_sakit' => 0,   
        ];

        // 4. Loop User & Tanggal untuk menyusun Report
        foreach ($users as $user) {
            // Ambil koleksi data user ini dari hasil query di atas
            $userRecords = $attendancesDB->get($user->id, collect());

            foreach ($period as $dateObj) {
                $dateString = $dateObj->format('Y-m-d');
                
                // Cari data di tanggal ini
                $record = $userRecords->firstWhere('date', $dateObj->startOfDay()); // startOfDay karena cast 'date' di model return Carbon object 00:00:00

                // Default Values (Jika belum di-sync)
                $clockInTime = '-';
                $clockOutTime = '-';
                $statusLabel = 'Belum Sync'; 
                $colorClass = 'text-gray-400 italic'; // Warna abu-abu untuk data kosong

                if ($record) {
                    $clockInTime = $record->clock_in ? Carbon::parse($record->clock_in)->format('H:i') : '-';
                    $clockOutTime = $record->clock_out ? Carbon::parse($record->clock_out)->format('H:i') : '-';
                    
                    // Mapping Status DB ke Label View & Warna
                    switch ($record->status) {
                        case 'present':
                            $statusLabel = 'Hadir';
                            $colorClass = 'text-green-600 font-bold';
                            $summary['hadir']++;
                            break;
                        case 'late':
                            $statusLabel = 'Terlambat';
                            $colorClass = 'text-yellow-600 font-bold';
                            $summary['terlambat']++;
                            // Jika ada catatan pulang cepat, hitung juga
                            if(str_contains($record->note, 'Pulang Cepat')) $summary['pulang_cepat']++;
                            break;
                        case 'early_leave':
                            $statusLabel = 'Pulang Cepat';
                            $colorClass = 'text-orange-600 font-bold';
                            $summary['pulang_cepat']++;
                            break;
                        case 'no_in':
                            $statusLabel = 'Belum Absen Datang';
                            $colorClass = 'text-purple-600 font-bold';
                            $summary['belum_datang']++;
                            break;
                        case 'no_out':
                            $statusLabel = 'Belum Absen Pulang';
                            $colorClass = 'text-purple-600 font-bold';
                            $summary['belum_pulang']++;
                            break;
                        case 'absent':
                            $statusLabel = 'Mangkir';
                            $colorClass = 'text-red-600 font-bold';
                            $summary['mangkir']++;
                            break;
                        case 'holiday':
                            $statusLabel = 'Libur';
                            // Use note if available (e.g. "Libur Akhir Pekan")
                            if ($record->note) {
                                $statusLabel = $record->note;
                            }
                            $colorClass = 'text-neutral-500 font-bold';
                            break;
                        case 'leave':
                        case 'sick':
                        case 'permit':
                            $statusLabel = ucfirst($record->status); // Cuti/Sakit
                            // Jika detail ada di note (misal: "Cuti Tahunan"), gunakan note
                            if ($record->note && stripos($record->status, 'leave') !== false) {
                                $statusLabel = $record->note;
                            }
                            $colorClass = 'text-blue-600 font-bold';
                            
                            if (stripos($statusLabel, 'sakit') !== false || $record->status == 'sick') {
                                $summary['total_sakit']++;
                            } else {
                                $summary['total_cuti']++;
                            }
                            break;
                        default:
                            $statusLabel = ucfirst($record->status);
                            $colorClass = 'text-gray-600';
                    }

                    // Append Note jika ada (misal: "Terlambat (Pulang Cepat)")
                    if ($record->note && $record->status != 'leave') {
                        // Hindari duplikasi teks jika note sama dengan status
                        if (stripos($record->note, $statusLabel) === false) {
                            $statusLabel .= ' (' . $record->note . ')';
                        }
                    }
                }

                // Masukkan ke Array Report
                $attendanceReport[] = [
                    'user_name' => $user->name,
                    'date' => $dateString,
                    'day_name' => $dateObj->translatedFormat('l'),
                    'clock_in' => $clockInTime,
                    'clock_out' => $clockOutTime,
                    'status' => $statusLabel,
                    'color_class' => $colorClass,
                ];
            }
        }

        // 5. Filter Final Array (Client-side filtering logic di server)
        if ($selectedStatus) {
            $attendanceReport = collect($attendanceReport)->filter(function ($row) use ($selectedStatus) {
                // Pencarian string status agar fleksibel
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

    /**
     * SYNC: Memproses Raw Logs menjadi Data Matang (Tabel Attendances)
     * Dipanggil saat tombol Sync diklik.
     */
    public function sync(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::today()->format('Y-m-d'));
        $endDate   = $request->input('end_date', Carbon::today()->format('Y-m-d'));
        
        $users = User::whereHas('fingerprintUser')->get();
        $period = CarbonPeriod::create($startDate, $endDate);
        
        // Ambil data libur dalam rentang tanggal sekalian biar efisien
        $holidays = Holiday::whereBetween('date', [$startDate, $endDate])
                    ->get()
                    ->keyBy(fn($item) => $item->date->format('Y-m-d')); // Key by date string

        // Settings Kebijakan Jam
        $jamMasukToleransi = '08:15'; 
        $jamPulangNormal   = '13:00'; 
        $jamPulangJumat    = '11:00'; 

        $counter = 0;

        foreach ($users as $user) {
            $machineId = $user->fingerprintUser->user_id_machine ?? null;
            if(!$machineId) continue;

            foreach ($period as $date) {
                // Jangan sync hari masa depan
                if ($date->isFuture()) continue;

                $dateString = $date->format('Y-m-d');

                // --- 1. Ambil Data Pendukung ---
                $dayLogs = AttendanceLog::where('user_id_machine', $machineId)
                    ->whereDate('timestamp', $dateString)
                    ->orderBy('timestamp', 'asc')
                    ->get();

                $isLeave = LeaveRequest::where('user_id', $user->id)
                    ->where('status', 'approved')
                    ->where(fn($q) => $q->whereDate('start_date', '<=', $dateString)
                                        ->whereDate('end_date', '>=', $dateString))
                    ->first();

                // Cek Libur dari Collection yang sudah diambil di awal
                $holidayData = $holidays->get($dateString); 
                $isHoliday = $holidayData ? true : false;
                $isWeekend = $date->isWeekend();

                // --- 2. Tentukan Status & Jam ---
                $clockIn = null;
                $clockOut = null;
                $status = 'absent'; // Default Mangkir
                $note = null;

                // Priority A: Cuti
                if ($isLeave) {
                    $status = 'leave'; 
                    $note = $isLeave->leave_type; 
                    if (stripos($isLeave->leave_type, 'sakit') !== false) {
                        $status = 'sick';
                    }
                } 
                // Priority B: Hari Libur (Weekend/Holiday)
                elseif ($isHoliday || $isWeekend) {
                    // Jika ada log di hari libur -> Lembur (Masuk)
                    if ($dayLogs->count() > 0) {
                        // Proses data jam masuk/keluar seperti biasa
                        $morningLogs = $dayLogs->filter(fn($log) => $log->timestamp->hour < 10);
                        $afternoonLogs = $dayLogs->filter(fn($log) => $log->timestamp->hour >= 10);

                        $inLog = $morningLogs->first(); 
                        $outLog = $afternoonLogs->last();

                        if ($inLog) $clockIn = $inLog->timestamp->format('H:i:s');
                        if ($outLog) $clockOut = $outLog->timestamp->format('H:i:s');
                        
                        $status = 'present'; // Tetap status hadir
                        $note = 'Lembur / Masuk Libur';
                    } else {
                        // Jika TIDAK ada log, set status khusus agar tidak dianggap 'absent' atau 'belum sync'
                        $status = 'holiday'; 
                        if ($isHoliday) {
                            $note = 'Libur: ' . $holidayData->title;
                        } else {
                            $note = 'Libur Akhir Pekan';
                        }
                    }
                }
                // Priority C: Ada Log Absensi (Hari Kerja Biasa)
                elseif ($dayLogs->count() > 0) {
                    $morningLogs = $dayLogs->filter(fn($log) => $log->timestamp->hour < 10);
                    $afternoonLogs = $dayLogs->filter(fn($log) => $log->timestamp->hour >= 10);

                    $inLog = $morningLogs->first(); 
                    $outLog = $afternoonLogs->last();

                    if ($inLog) $clockIn = $inLog->timestamp->format('H:i:s');
                    if ($outLog) $clockOut = $outLog->timestamp->format('H:i:s');

                    $status = 'present'; // Default Hadir Tepat Waktu

                    // Cek Kelengkapan & Ketepatan
                    if ($clockIn && $clockOut) {
                        $targetPulang = $date->isFriday() ? $jamPulangJumat : $jamPulangNormal;
                        
                        $isLate = $clockIn > $jamMasukToleransi;
                        $isEarly = $clockOut < $targetPulang;

                        if ($isLate && $isEarly) {
                            $status = 'late'; // Prioritas status utama
                            $note = 'Terlambat, Pulang Cepat';
                        } elseif ($isLate) {
                            $status = 'late';
                            $note = 'Terlambat';
                        } elseif ($isEarly) {
                            $status = 'early_leave';
                            $note = 'Pulang Cepat';
                        }
                    } elseif ($clockIn && !$clockOut) {
                        $status = 'no_out'; // Lupa Absen Pulang
                    } elseif (!$clockIn && $clockOut) {
                        $status = 'no_in'; // Lupa Absen Datang
                    }
                }

                // --- 3. Simpan ke Tabel Attendances ---
                Attendance::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'date'    => $dateString
                    ],
                    [
                        'clock_in'  => $clockIn,
                        'clock_out' => $clockOut,
                        'status'    => $status,
                        'note'      => $note
                    ]
                );
                
                $counter++;
            }
        }

        return back()->with('success', "Sinkronisasi selesai! $counter data berhasil diproses.");
    }
}