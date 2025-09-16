<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LeaveType;
use App\Models\UserLeaveQuota;

class UserLeaveQuotaController extends Controller
{
    public function show($leaveTypeId)
    {
        $user = Auth::user();
        $leaveType = LeaveType::find($leaveTypeId);

        if (!$leaveType || $leaveType->kuota <= 0) {
            return response()->json(['sisa_kuota' => '-']);
        }

        $quota = UserLeaveQuota::firstOrCreate(
            ['user_id' => $user->id, 'leave_type_id' => $leaveType->id, 'tahun' => now()->year],
            ['jumlah_diambil' => 0]
        );

        $sisaKuota = $leaveType->kuota - $quota->jumlah_diambil;

        return response()->json(['sisa_kuota' => $sisaKuota . ' ' . $leaveType->satuan]);
    }
}
