<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 1. Gate: Akses Masuk Panel Admin (Dashboard & Laporan)
        // Superadmin, SysAdmin, dan Unit Admin boleh masuk
        Gate::define('access-admin-panel', function (User $user) {
            return in_array($user->role, ['superadmin', 'sys_admin', 'unit_admin']);
        });

        // 2. Gate: Kelola Master Data (User, Unit Kerja, Jabatan, Libur)
        // Unit Admin DILARANG akses ini (sesuai request)
        Gate::define('manage-master-data', function (User $user) {
            return in_array($user->role, ['superadmin', 'sys_admin']);
        });

        // 3. Gate: Melihat Semua Data (Lintas Unit)
        // Hanya Superadmin & SysAdmin yang bisa lihat semua unit
        Gate::define('view-all-units', function (User $user) {
            return in_array($user->role, ['superadmin', 'sys_admin']);
        });

        // 4. Gate: Proteksi Superadmin
        // Hanya Superadmin yang boleh mengelola sesama Superadmin
        Gate::define('manage-superadmin', function (User $user) {
            return $user->role === 'superadmin';
        });
    }
}
