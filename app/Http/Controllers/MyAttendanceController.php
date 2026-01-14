<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Models\Holiday;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class MyAttendanceController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user->fingerprintUser) {
            return view('my-attendance.unmapped');
        }

        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        
        // 1. Ambil Data Matang
        $attendancesDB = Attendance::where('user_id', $user->id)
            ->whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $endDate)
            ->get()
            ->keyBy(fn($item) => $item->date->format('Y-m-d'));

        $attendanceHistory = [];
        $period = CarbonPeriod::create($startDate, $endDate);

        // --- INIT SUMMARY ---
        $summary = [
            'hadir' => 0,
            'terlambat' => 0,
            'mangkir' => 0,
            'izin_cuti' => 0,
            'sakit' => 0,
            'data_tidak_lengkap' => 0
        ];

        foreach ($period as $dateObj) {
            $dateString = $dateObj->format('Y-m-d');
            
            // --- VARIABEL DEFAULT (PENTING: Diinisialisasi di sini) ---
            $clockInTime = '-';
            $clockOutTime = '-';
            
            // Status Default jika belum di-sync: "Belum Di-rekap"
            // Kita pakai variabel terpisah agar tidak campur aduk antara label view dan logic hitung
            $statusLabel = 'Belum Di-rekap'; 
            $colorClass = 'text-gray-400 border-gray-200 bg-gray-50'; 

            // --- A. CEK DATABASE ---
            if ($attendancesDB->has($dateString)) {
                $record = $attendancesDB->get($dateString);
                $clockInTime = $record->clock_in ? Carbon::parse($record->clock_in)->format('H:i') : '-';
                $clockOutTime = $record->clock_out ? Carbon::parse($record->clock_out)->format('H:i') : '-';
                
                switch ($record->status) {
                    case 'present': 
                        $statusLabel = 'Hadir'; 
                        $colorClass = 'text-green-700 border-green-200 bg-green-50'; 
                        break;
                    case 'late': 
                        $statusLabel = 'Terlambat'; 
                        $colorClass = 'text-yellow-700 border-yellow-200 bg-yellow-50'; 
                        break;
                    case 'early_leave': 
                        $statusLabel = 'Pulang Cepat'; 
                        $colorClass = 'text-orange-700 border-orange-200 bg-orange-50'; 
                        break;
                    case 'no_in': 
                        $statusLabel = 'Blm Absen Datang'; 
                        $colorClass = 'text-purple-700 border-purple-200 bg-purple-50'; 
                        break;
                    case 'no_out': 
                        $statusLabel = 'Blm Absen Pulang'; 
                        $colorClass = 'text-purple-700 border-purple-200 bg-purple-50'; 
                        break;
                    case 'absent': 
                        $statusLabel = 'Mangkir'; 
                        $colorClass = 'text-red-700 border-red-200 bg-red-50'; 
                        break;
                    case 'leave': 
                    case 'sick': 
                    case 'permit': 
                        $statusLabel = ucfirst($record->status); 
                        if($record->note && stripos($record->status, 'leave') !== false) {
                            $statusLabel = $record->note;
                        }
                        $colorClass = 'text-blue-700 border-blue-200 bg-blue-50'; 
                        break;
                    case 'holiday':
                        $statusLabel = $record->note ?? 'Libur';
                        $colorClass = 'text-neutral-500 border-neutral-200 bg-neutral-50';
                        break;
                    default: 
                        $statusLabel = ucfirst($record->status); 
                        $colorClass = 'text-gray-600 border-gray-200 bg-gray-50';
                }

                // Tambahan Note
                if ($record->note && !in_array($record->status, ['leave', 'holiday'])) {
                    if (stripos($record->note, $statusLabel) === false) {
                        $statusLabel .= ' (' . $record->note . ')';
                    }
                }
            }

            // --- B. UPDATE COUNTER SUMMARY ---
            // Kita hitung berdasarkan $statusLabel yang sudah diproses di atas
            // Hanya hitung jika status BUKAN "Belum Di-rekap"
            if ($statusLabel !== 'Belum Di-rekap') {
                if (Str::contains($statusLabel, ['Hadir', 'Lembur', 'Pulang Cepat'], true)) {
                    $summary['hadir']++;
                }
                if (Str::contains($statusLabel, 'Terlambat', true)) {
                    $summary['terlambat']++;
                }
                if (Str::contains($statusLabel, 'Mangkir', true)) {
                    $summary['mangkir']++;
                }
                if (Str::contains($statusLabel, ['Cuti', 'Izin', 'Sakit'], true)) {
                    $summary['izin_cuti']++;
                    if (Str::contains($statusLabel, 'Sakit', true)) $summary['sakit']++;
                }
                if (Str::contains($statusLabel, ['Blm Absen', 'Belum Absen'], true)) {
                    $summary['data_tidak_lengkap']++;
                }
            }

            array_unshift($attendanceHistory, [
                'date' => $dateString,
                'day_name' => $dateObj->translatedFormat('l'),
                'clock_in' => $clockInTime,
                'clock_out' => $clockOutTime,
                'status' => $statusLabel,
                'color_class' => $colorClass,
            ]);
        }

        return view('my-attendance.index', compact('attendanceHistory', 'startDate', 'endDate', 'summary'));
    }
}