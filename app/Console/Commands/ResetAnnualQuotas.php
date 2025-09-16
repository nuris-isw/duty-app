<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LeaveType;
use App\Models\UserLeaveQuota;
use Carbon\Carbon;

class ResetAnnualQuotas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'quota:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mereset jatah cuti tahunan untuk semua pegawai';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai proses reset kuota cuti tahunan...');

        // 1. Cari semua jenis cuti yang periode reset-nya 'tahunan'
        $resettableTypeIds = LeaveType::where('periode_reset', 'tahunan')->pluck('id');

        if ($resettableTypeIds->isEmpty()) {
            $this->info('Tidak ada jenis cuti yang perlu direset. Selesai.');
            return 0;
        }

        // 2. Tentukan tahun saat ini
        $currentYear = Carbon::now()->year;

        // 3. Update 'jumlah_diambil' menjadi 0 untuk semua kuota tahunan dari TAHUN LALU
        //    Ini untuk memastikan data historis tidak berubah. Kuota baru akan dibuat saat pegawai mengajukan.
        //    ATAU, cara yang lebih proaktif adalah membuat kuota baru untuk tahun ini. Mari kita lakukan itu.

        // 3. Ambil semua user (pegawai & atasan)
        $users = \App\Models\User::whereIn('role', ['pegawai', 'atasan'])->get();
        $newQuotasCreated = 0;

        foreach ($users as $user) {
            foreach ($resettableTypeIds as $typeId) {
                // Cari atau buat kuota baru untuk tahun ini
                UserLeaveQuota::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'leave_type_id' => $typeId,
                        'tahun' => $currentYear,
                    ],
                    [
                        'jumlah_diambil' => 0
                    ]
                );
                $newQuotasCreated++;
            }
        }
        
        $this->info("Proses reset selesai. {$newQuotasCreated} kuota untuk tahun {$currentYear} telah dibuat/diperbarui.");
        return 0;
    }
}
