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
use Barryvdh\DomPDF\Facade\Pdf; // Pastikan library PDF sudah diimport

class AttendanceRecapController extends Controller
{
    /**
     * Method Utama: Memproses semua logika data.
     * Digunakan oleh Index (View HTML) dan Print (PDF).
     */
    private function getData(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $users = User::whereHas('fingerprintUser')->orderBy('name')->get();

        $allLogs = AttendanceLog::whereDate('timestamp', '>=', $startDate)
            ->whereDate('timestamp', '<=', $endDate)
            ->orderBy('timestamp', 'asc')
            ->get();

        $allLeaves = LeaveRequest::where('status', 'approved')
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                      ->orWhereBetween('end_date', [$startDate, $endDate]);
            })->get();

        $holidays = Holiday::whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $endDate)
            ->get()
            ->keyBy(fn($item) => Carbon::parse($item->date)->format('Y-m-d'));

        $recap = [];
        $summaryData = [];
        $period = CarbonPeriod::create($startDate, $endDate);
        
        $today = Carbon::today();
        $jamMasukToleransi = '08:15'; 
        $jamPulangNormal   = '13:00'; 
        $jamPulangJumat    = '11:00'; 

        foreach ($users as $user) {
            $machineId = $user->fingerprintUser->user_id_machine;
            $userLogs = $allLogs->where('user_id_machine', $machineId);
            $userLeaves = $allLeaves->where('user_id', $user->id);

            // Init Counter
            $summaryData[$user->id] = [
                'hadir' => 0, 'terlambat' => 0, 'pulang_awal' => 0,
                'no_in' => 0, 'no_out' => 0, 'cuti' => 0, 'sakit' => 0, 'mangkir' => 0
            ];

            foreach ($period as $date) {
                $dateString = $date->format('Y-m-d');
                $targetPulang = $date->isFriday() ? $jamPulangJumat : $jamPulangNormal;
                
                // Logic Status Dasar
                $status = $date->lt($today) ? 'red' : 'empty';
                $tooltip = $date->lt($today) ? 'Mangkir / Tanpa Keterangan' : '-';
                $isMangkir = $date->lt($today);

                // 1. Cek Libur
                if ($date->isWeekend() || isset($holidays[$dateString])) {
                    $status = isset($holidays[$dateString]) ? 'holiday' : 'gray';
                    $tooltip = isset($holidays[$dateString]) ? 'Libur: '.$holidays[$dateString]->title : 'Libur Akhir Pekan';
                    $isMangkir = false;
                }

                // 2. Cek Cuti
                $isLeave = $userLeaves->filter(fn($l) => $dateString >= $l->start_date && $dateString <= $l->end_date)->first();
                if ($isLeave) {
                    $status = 'blue';
                    $tooltip = 'Cuti/Izin: ' . $isLeave->reason;
                    $isMangkir = false;
                    
                    if (stripos($isLeave->leave_type, 'sakit') !== false) {
                        $summaryData[$user->id]['sakit']++;
                    } else {
                        $summaryData[$user->id]['cuti']++;
                    }
                }

                // 3. Cek Log Absensi
                $dayLogs = $userLogs->filter(fn($log) => str_starts_with($log->timestamp, $dateString));

                if ($dayLogs->count() > 0) {
                    $isMangkir = false;
                    $summaryData[$user->id]['hadir']++;

                    $firstLog = $dayLogs->first();
                    $lastLog = $dayLogs->last(); 
                    $inTime = $firstLog ? Carbon::parse($firstLog->timestamp)->format('H:i') : null;
                    $outTime = ($dayLogs->count() > 1) ? Carbon::parse($lastLog->timestamp)->format('H:i') : null;

                    if ($inTime && $outTime) {
                        $ket = [];
                        if ($inTime > $jamMasukToleransi) {
                            $ket[] = "Telat ($inTime)";
                            $summaryData[$user->id]['terlambat']++;
                        }
                        if ($outTime < $targetPulang) {
                            $ket[] = "Plg Cepat ($outTime)";
                            $summaryData[$user->id]['pulang_awal']++;
                        }

                        $status = !empty($ket) ? 'orange' : 'green';
                        $tooltip = !empty($ket) ? "Kurang Disiplin: " . implode(', ', $ket) : "Hadir Lengkap ($inTime - $outTime)";
                    } else {
                        $status = 'yellow';
                        if ($inTime && !$outTime) {
                            $tooltip = "Lupa Absen Pulang (Masuk: $inTime)";
                            $summaryData[$user->id]['no_out']++;
                            if ($inTime > $jamMasukToleransi) $summaryData[$user->id]['terlambat']++;
                        } elseif (!$inTime && $outTime) {
                            $tooltip = "Lupa Absen Masuk (Pulang: $outTime)";
                            $summaryData[$user->id]['no_in']++;
                        }
                    }
                }

                if ($isMangkir) $summaryData[$user->id]['mangkir']++;

                $recap[$user->id][$dateString] = ['color' => $status, 'tooltip' => $tooltip];
            }
        }

        // Return semua data yang dibutuhkan View/PDF
        return compact('users', 'recap', 'period', 'startDate', 'endDate', 'summaryData');
    }

    // --- 3 METHOD PUBLIK YANG RINGKAS ---

    public function index(Request $request)
    {
        $data = $this->getData($request); // Panggil fungsi private di atas
        return view('admin.reports.rekap_absensi.index', $data);
    }

    public function printSummary(Request $request)
    {
        $data = $this->getData($request);
        $pdf = Pdf::loadView('admin.reports.rekap_absensi.print_summary', $data)
                  ->setPaper('a4', 'portrait');
        return $pdf->stream('Rekap_Total_Absensi.pdf');
    }

    public function printMatrix(Request $request)
    {
        $data = $this->getData($request);
        $pdf = Pdf::loadView('admin.reports.rekap_absensi.print_matrix', $data)
                  ->setPaper('a4', 'landscape');
        return $pdf->stream('Matriks_Absensi.pdf');
    }
}