<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\UserLeaveQuotaController;
use App\Http\Controllers\MyAttendanceController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\AttendanceCorrectionController;

// Admin Controllers
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\UnitKerjaController;
use App\Http\Controllers\Admin\JabatanController;
use App\Http\Controllers\Admin\LeaveTypeController;
use App\Http\Controllers\Admin\LeaveRequestController as AdminLeaveRequestController;
use App\Http\Controllers\Admin\FingerprintUserController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\HolidayController;
use App\Http\Controllers\Admin\AttendanceRecapController;

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

// Route Auth Google
Route::get('/auth/google/redirect', [GoogleController::class, 'redirect'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('google.callback');

// Dashboard Umum (Semua User Login)
// Controller dashboard harus menangani logika tampilan per role jika diperlukan
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('dashboard');

// ====================================================
// GROUP 1: FITUR PEGAWAI / UMUM (Authenticated Users)
// ====================================================
Route::middleware('auth')->group(function () {
    // Profil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/signature', [ProfileController::class, 'updateSignature'])->name('profile.signature.update');

    // Absensi Pribadi
    Route::get('/my-attendance', [MyAttendanceController::class, 'index'])->name('my-attendance.index');

    // Pengajuan Cuti (User)
    Route::get('/leave-requests/create', [LeaveRequestController::class, 'create'])->name('leave-requests.create');
    Route::post('/leave-requests', [LeaveRequestController::class, 'store'])->name('leave-requests.store');
    
    // Approval Cuti (Fungsional Atasan)
    // Pastikan Controller memvalidasi relasi atasan-bawahan
    Route::patch('/leave-requests/{leaveRequest}/approve', [LeaveRequestController::class, 'approve'])->name('leave-requests.approve');
    Route::patch('/leave-requests/{leaveRequest}/reject', [LeaveRequestController::class, 'reject'])->name('leave-requests.reject');
    
    // Utilitas Cuti
    Route::get('/leave-requests/{leaveRequest}/print', [LeaveRequestController::class, 'print'])->name('leave-requests.print');
    Route::get('/leave-quotas/{leaveTypeId}', [UserLeaveQuotaController::class, 'show'])->name('leave-quotas.show');

    // Kalender (Controller menangani scoping unit kerja)
    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');
    Route::get('/calendar/events', [CalendarController::class, 'events'])->name('calendar.events');

    // Koreksi Absensi
    Route::resource('attendance-correction', AttendanceCorrectionController::class)->only(['index', 'create', 'store']);
    Route::patch('/attendance-correction/{correctionRequest}/approve', [AttendanceCorrectionController::class, 'approve'])->name('attendance-correction.approve');
    Route::patch('/attendance-correction/{correctionRequest}/reject', [AttendanceCorrectionController::class, 'reject'])->name('attendance-correction.reject');
});

// ====================================================
// GROUP 2: ADMIN PANEL (RBAC Implementation)
// ====================================================
Route::middleware(['auth', 'verified', 'can:access-admin-panel']) // Gate: Superadmin, SysAdmin, Unit Admin
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // ------------------------------------------------
        // SUB-GROUP A: LAPORAN (Bisa diakses Unit Admin)
        // ------------------------------------------------
        
        // Laporan Cuti
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/print', [ReportController::class, 'print'])->name('reports.print');
        Route::get('/leave-requests/{leaveRequest}/print', [AdminLeaveRequestController::class, 'print'])->name('leave-requests.print');
        
        // Laporan Absensi Harian
        Route::get('/attendance/report', [AttendanceController::class, 'index'])->name('attendance.report');
        Route::post('/attendance/sync', [AttendanceController::class, 'sync'])->name('attendance.sync');

        // Rekapitulasi Matriks (Bulanan) & PDF
        Route::get('/rekap-absensi', [AttendanceRecapController::class, 'index'])->name('rekap-absensi.index');
        Route::get('/rekap-absensi/print-summary', [AttendanceRecapController::class, 'printSummary'])->name('rekap-absensi.print-summary');
        Route::get('/rekap-absensi/print-matrix', [AttendanceRecapController::class, 'printMatrix'])->name('rekap-absensi.print-matrix');


        // ------------------------------------------------
        // SUB-GROUP B: MASTER DATA & SETTINGS (Restricted)
        // Hanya Superadmin & SysAdmin (Unit Admin DITOLAK)
        // ------------------------------------------------
        Route::middleware(['can:manage-master-data'])->group(function () {
            
            // Manajemen User & Organisasi
            Route::resource('users', UserController::class);
            Route::resource('unit-kerja', UnitKerjaController::class);
            Route::resource('jabatan', JabatanController::class);
            
            // Manajemen Cuti (Admin)
            Route::resource('leave-types', LeaveTypeController::class);
            Route::resource('leave-requests', AdminLeaveRequestController::class); // CRUD Full Cuti
            
            // Settings Absensi
            Route::get('/attendance/mapping', [FingerprintUserController::class, 'index'])->name('attendance.mapping');
            Route::put('/attendance/mapping/{id}', [FingerprintUserController::class, 'update'])->name('attendance.mapping.update');
            
            // Settings Hari Libur
            Route::get('/holidays', [HolidayController::class, 'index'])->name('holidays.index');
            Route::post('/holidays', [HolidayController::class, 'store'])->name('holidays.store');
            Route::delete('/holidays/{id}', [HolidayController::class, 'destroy'])->name('holidays.destroy');
        });

    });

require __DIR__.'/auth.php';