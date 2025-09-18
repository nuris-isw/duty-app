<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;

class LeaveRequestController extends Controller
{
    public function edit(LeaveRequest $leaveRequest)
    {
        return view('admin.leave-requests.edit', compact('leaveRequest'));
    }

    public function update(Request $request, LeaveRequest $leaveRequest)
    {
        $validatedData = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string',
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $leaveRequest->update($validatedData);

        return redirect()->route('admin.reports.index')->with('success', 'Pengajuan berhasil diperbarui.');
    }

    public function destroy(LeaveRequest $leaveRequest)
    {
        $leaveRequest->delete();
        return redirect()->route('admin.reports.index')->with('success', 'Pengajuan berhasil dihapus.');
    }
}
