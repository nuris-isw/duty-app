<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\UnitKerjaController;
use App\Http\Controllers\Admin\JabatanController;
use App\Http\Controllers\Admin\LeaveTypeController;
use App\Http\Controllers\UserLeaveQuotaController;
use App\Http\Controllers\CalendarController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Gate;

Gate::define('is-admin', function ($user) {
    return $user->role === 'admin';
});

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    // Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::patch('/profile/signature', [ProfileController::class, 'updateSignature'])->name('profile.signature.update');

    Route::get('/leave-requests/create', [LeaveRequestController::class, 'create'])->name('leave-requests.create');
    Route::post('/leave-requests', [LeaveRequestController::class, 'store'])->name('leave-requests.store');
    // Route untuk atasan menyetujui atau menolak
    Route::patch('/leave-requests/{leaveRequest}/approve', [LeaveRequestController::class, 'approve'])->name('leave-requests.approve');
    Route::patch('/leave-requests/{leaveRequest}/reject', [LeaveRequestController::class, 'reject'])->name('leave-requests.reject');
    // Route untuk mencetak PDF
    Route::get('/leave-requests/{leaveRequest}/print', [LeaveRequestController::class, 'print'])->name('leave-requests.print');
    // Route untuk mengambil sisa kuota via JavaScript
    Route::get('/leave-quotas/{leaveTypeId}', [UserLeaveQuotaController::class, 'show'])->name('leave-quotas.show');
    // Route untuk menampilkan halaman kalender
    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');
    // Route API untuk data event kalender
    Route::get('/calendar/events', [CalendarController::class, 'events'])->name('calendar.events');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Menggunakan Route::resource akan otomatis membuat route index, create, store, dll.
    Route::resource('users', UserController::class);
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/print', [ReportController::class, 'print'])->name('reports.print');
    Route::resource('unit-kerja', UnitKerjaController::class);
    Route::resource('jabatan', JabatanController::class);
    Route::resource('leave-types', LeaveTypeController::class);
});

Route::get('/auth/google/redirect', [GoogleController::class, 'redirect'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('google.callback');


require __DIR__.'/auth.php';
