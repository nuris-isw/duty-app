<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Holiday;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Barryvdh\DomPDF\Facade\Pdf;

class AttendanceRecapController extends Controller
{
    private function getData(Request $request)
    {
        $currentUser = Auth::user();
        
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        // --- 1. SCOPING USER (PERBAIKAN: Filter Administrator) ---
        $usersQuery = User::orderBy('name')
            ->where('role', '!=', 'superadmin'); // 1. Exclude Superadmin secara global

        // Opsi: Jika Sys Admin juga tidak perlu diabsen, uncomment baris bawah:
        // $usersQuery->where('role', '!=', 'sys_admin');

        if (Gate::allows('view-all-units')) {
            // Logic tambahan jika ada
        } 
        elseif ($currentUser->role === 'unit_admin') {
            $usersQuery->where('unit_kerja_id', $currentUser->unit_kerja_id);
        } 
        else {
            // User biasa hanya melihat diri sendiri
            $usersQuery->where('id', $currentUser->id);
        }

        $users = $usersQuery->get();
        $userIds = $users->pluck('id')->toArray();

        // --- 2. AMBIL DATA ATTENDANCE ---
        $allAttendances = Attendance::whereIn('user_id', $userIds)
            ->whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $endDate)
            ->get()
            ->groupBy(function($item) {
                return $item->user_id . '-' . $item->date->format('Y-m-d');
            });

        // --- 3. AMBIL DATA LIBUR ---
        $holidays = Holiday::whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $endDate)
            ->get()
            ->keyBy(fn($item) => Carbon::parse($item->date)->format('Y-m-d'));

        $recap = [];
        $summaryData = [];
        $period = CarbonPeriod::create($startDate, $endDate);
        $today = Carbon::today();

        foreach ($users as $user) {
            // Init Summary
            $summaryData[$user->id] = [
                'hadir' => 0, 'terlambat' => 0, 'pulang_awal' => 0,
                'no_in' => 0, 'no_out' => 0, 'cuti' => 0, 'sakit' => 0, 'mangkir' => 0
            ];

            foreach ($period as $date) {
                $dateString = $date->format('Y-m-d');
                $lookupKey = $user->id . '-' . $dateString;
                
                $color = 'empty'; 
                $tooltip = '-';
                $statusForCount = '';

                // --- LOGIKA PENENTUAN STATUS ---

                // KONDISI 1: ADA DATA DI DATABASE (Prioritas Utama)
                if ($allAttendances->has($lookupKey)) {
                    $record = $allAttendances->get($lookupKey)->first();
                    $dbStatus = $record->status;
                    $note = $record->note;

                    $in  = $record->clock_in ? Carbon::parse($record->clock_in)->format('H:i') : '-';
                    $out = $record->clock_out ? Carbon::parse($record->clock_out)->format('H:i') : '-';

                    switch ($dbStatus) {
                        case 'present':
                            $color = 'green';
                            $tooltip = "Hadir ($in - $out)";
                            $statusForCount = 'Hadir';
                            break;
                        case 'late':
                            $color = 'orange';
                            $tooltip = "Telat ($in - $out)";
                            $statusForCount = 'Terlambat';
                            break;
                        case 'early_leave':
                            $color = 'orange';
                            $tooltip = "Pulang Cepat ($in - $out)";
                            $statusForCount = 'Pulang Cepat';
                            break;
                        case 'no_in':
                            $color = 'yellow';
                            $tooltip = "Lupa Absen Datang (Plg: $out)";
                            $statusForCount = 'No In';
                            break;
                        case 'no_out':
                            $color = 'yellow';
                            $tooltip = "Lupa Absen Pulang (Msk: $in)";
                            $statusForCount = 'No Out';
                            break;
                        case 'sick':
                            $color = 'blue';
                            $tooltip = "Sakit: " . ($note ?? '-');
                            $statusForCount = 'Sakit';
                            break;
                        case 'leave':
                        case 'permit':
                            $color = 'blue';
                            $tooltip = "Cuti/Izin: " . ($note ?? '-');
                            $statusForCount = 'Cuti';
                            break;
                        case 'holiday':
                            // Jika di DB statusnya holiday (hasil sync), cek apakah weekend atau nasional
                            // Agar warnanya konsisten
                            if (isset($holidays[$dateString])) {
                                $color = 'holiday'; // Warna Khusus Libur Nasional
                                $tooltip = "Libur: " . ($note ?? $holidays[$dateString]->title);
                            } else {
                                $color = 'gray'; // Kemungkinan Libur Akhir Pekan yg tersimpan di DB
                                $tooltip = "Libur Akhir Pekan";
                            }
                            break;
                        case 'absent':
                        case 'alpha':
                            $color = 'red';
                            $tooltip = "Mangkir / Tanpa Keterangan";
                            $statusForCount = 'Mangkir';
                            break;
                        default:
                            $color = 'gray';
                            $tooltip = ucfirst($dbStatus);
                    }

                } else {
                    // KONDISI 2: TIDAK ADA DATA (Row Kosong)
                    // Fallback Logic (Cek Kalender)

                    if ($date->gt($today)) {
                        // Masa Depan
                        $color = 'empty'; 
                        $tooltip = '-';
                    } 
                    // PERBAIKAN: Membedakan Libur Nasional vs Akhir Pekan
                    elseif (isset($holidays[$dateString])) {
                        // Prioritas 1: Libur Nasional (Tanggal Merah Kalender)
                        $color = 'holiday'; 
                        $tooltip = "Libur: " . $holidays[$dateString]->title;
                    } 
                    elseif ($date->isWeekend()) {
                        // Prioritas 2: Akhir Pekan (Sabtu/Minggu)
                        $color = 'gray'; 
                        $tooltip = "Libur Akhir Pekan";
                    } 
                    else {
                        // Prioritas 3: Hari Kerja tapi Kosong = Mangkir
                        $color = 'red';
                        $tooltip = "Mangkir (Tidak Ada Data)";
                        $statusForCount = 'Mangkir';
                    }
                }

                // --- UPDATE SUMMARY COUNTER ---
                if ($statusForCount === 'Hadir') $summaryData[$user->id]['hadir']++;
                if ($statusForCount === 'Terlambat') {
                    $summaryData[$user->id]['hadir']++;
                    $summaryData[$user->id]['terlambat']++;
                }
                if ($statusForCount === 'Pulang Cepat') {
                    $summaryData[$user->id]['hadir']++;
                    $summaryData[$user->id]['pulang_awal']++;
                }
                if ($statusForCount === 'No In') $summaryData[$user->id]['no_in']++;
                if ($statusForCount === 'No Out') $summaryData[$user->id]['no_out']++;
                if ($statusForCount === 'Cuti') $summaryData[$user->id]['cuti']++;
                if ($statusForCount === 'Sakit') $summaryData[$user->id]['sakit']++;
                if ($statusForCount === 'Mangkir') $summaryData[$user->id]['mangkir']++;

                $recap[$user->id][$dateString] = ['color' => $color, 'tooltip' => $tooltip];
            }
        }

        return compact('users', 'recap', 'period', 'startDate', 'endDate', 'summaryData');
    }

    public function index(Request $request) {
        $data = $this->getData($request);
        return view('admin.reports.rekap_absensi.index', $data);
    }

    public function printSummary(Request $request) {
        $data = $this->getData($request);
        $pdf = Pdf::loadView('admin.reports.rekap_absensi.print_summary', $data)->setPaper('a4', 'portrait');
        return $pdf->stream('Rekap_Total_Absensi.pdf');
    }

    public function printMatrix(Request $request) {
        $data = $this->getData($request);
        $pdf = Pdf::loadView('admin.reports.rekap_absensi.print_matrix', $data)->setPaper('a4', 'landscape');
        return $pdf->stream('Matriks_Absensi.pdf');
    }
}