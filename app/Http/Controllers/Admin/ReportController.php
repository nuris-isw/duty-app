<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LeaveRequest;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // Ambil tanggal awal dan akhir dari input filter
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Mulai query ke model LeaveRequest
        $query = LeaveRequest::with(['user', 'user.jabatan', 'user.unitKerja']);

        // Jika ada input tanggal, tambahkan kondisi whereBetween
        if ($startDate && $endDate) {
            $query->whereBetween('start_date', [$startDate, $endDate]);
        }

        // Ambil data hasil query, urutkan berdasarkan yang terbaru
        $leaveRequests = $query->orderBy('created_at', 'desc')->get();

        // Kirim data ke view
        return view('admin.reports.index', [
            'leaveRequests' => $leaveRequests,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }

    public function print(Request $request)
    {
        // Logika query sama persis dengan method index()
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = LeaveRequest::with(['user', 'user.jabatan']);

        if ($startDate && $endDate) {
            $query->whereBetween('start_date', [$startDate, $endDate]);
        }

        $leaveRequests = $query->orderBy('created_at', 'desc')->get();

        // Siapkan data untuk dikirim ke view PDF
        $data = [
            'leaveRequests' => $leaveRequests,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ];

        // Generate PDF
        $pdf = PDF::loadView('admin.reports.pdf', $data);

        // Tampilkan atau unduh PDF
        return $pdf->stream('laporan-cuti.pdf');
    }
}
