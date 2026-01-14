<?php

namespace App\Http\Controllers;

use App\Models\Attendance; // Model Rekap Harian
use App\Models\AttendanceCorrectionRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AttendanceCorrectionController extends Controller
{
    /**
     * Halaman List Pengajuan (User Biasa)
     */
    public function index()
    {
        $requests = AttendanceCorrectionRequest::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('attendance-correction.index', compact('requests'));
    }

    /**
     * Form Pengajuan Baru
     */
    public function create()
    {
        return view('attendance-correction.create');
    }

    /**
     * Simpan Pengajuan
     */
    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date|before_or_equal:today', // Tidak boleh masa depan
            'proposed_start_time' => 'nullable|date_format:H:i',
            'proposed_end_time' => 'nullable|date_format:H:i',
            'reason' => 'required|string|max:255',
            'dokumen_pendukung' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        // Cek apakah sudah ada request pending untuk tanggal yang sama
        $exists = AttendanceCorrectionRequest::where('user_id', Auth::id())
            ->where('date', $request->date)
            ->where('status', 'pending')
            ->exists();

        if ($exists) {
            return back()->withErrors(['date' => 'Anda sudah memiliki pengajuan pending untuk tanggal ini.'])->withInput();
        }

        // Upload Dokumen
        $path = null;
        if ($request->hasFile('dokumen_pendukung')) {
            $path = $request->file('dokumen_pendukung')->store('attendance_corrections', 'public');
        }

        AttendanceCorrectionRequest::create([
            'user_id' => Auth::id(),
            'date' => $request->date,
            'proposed_start_time' => $request->proposed_start_time,
            'proposed_end_time' => $request->proposed_end_time,
            'reason' => $request->reason,
            'dokumen_pendukung' => $path,
            'status' => 'pending',
        ]);

        // Notifikasi Email ke Atasan (Opsional - Bisa ditambahkan nanti)

        return redirect()->route('attendance-correction.index')->with('success', 'Pengajuan koreksi berhasil dikirim.');
    }

    /**
     * [ATASAN] Setujui Pengajuan
     */
    public function approve(AttendanceCorrectionRequest $correctionRequest)
    {
        // 1. Cek Otorisasi (Harus Atasan Langsung)
        if (Auth::id() !== $correctionRequest->user->atasan_id) {
            abort(403, 'Akses Ditolak');
        }

        if ($correctionRequest->status !== 'pending') {
            return back()->with('error', 'Pengajuan ini sudah diproses.');
        }

        DB::transaction(function () use ($correctionRequest) {
            // A. Update Status Request
            $correctionRequest->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

            // B. Update/Create Data di Tabel Utama (Attendances)
            // Cari data absensi hari itu (atau buat baru jika alpha)
            $attendance = Attendance::firstOrNew([
                'user_id' => $correctionRequest->user_id,
                'date' => $correctionRequest->date,
            ]);

            // Timpa jam jika ada usulan baru
            if ($correctionRequest->proposed_start_time) {
                $attendance->clock_in = $correctionRequest->proposed_start_time;
            }
            if ($correctionRequest->proposed_end_time) {
                $attendance->clock_out = $correctionRequest->proposed_end_time;
            }

            $attendance->status = 'present'; // Dianggap hadir jika dikoreksi
            $attendance->note = 'Koreksi: ' . $correctionRequest->reason;
            $attendance->save();
        });

        return back()->with('success', 'Pengajuan koreksi disetujui & data absensi diperbarui.');
    }

    /**
     * [ATASAN] Tolak Pengajuan
     */
    public function reject(Request $request, AttendanceCorrectionRequest $correctionRequest)
    {
        if (Auth::id() !== $correctionRequest->user->atasan_id) {
            abort(403, 'Akses Ditolak');
        }

        $request->validate(['rejection_reason' => 'required|string']);

        $correctionRequest->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        return back()->with('success', 'Pengajuan koreksi ditolak.');
    }
}
