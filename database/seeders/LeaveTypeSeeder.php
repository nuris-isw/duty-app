<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\LeaveType;

class LeaveTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus data lama untuk menghindari duplikat saat seeding ulang
        LeaveType::query()->delete();

        LeaveType::create([
            'nama_cuti' => 'Tahunan',
            'kuota' => 15,
            'satuan' => 'hari',
            'periode_reset' => 'tahunan',
            'memerlukan_dokumen' => false,
            'bisa_retroaktif' => false,
        ]);

        LeaveType::create([
            'nama_cuti' => 'Melahirkan',
            'kuota' => 90, // Durasi dalam hari
            'satuan' => 'hari', // Tetap hari, tapi akan kita batasi 1x per tahun di logika
            'periode_reset' => 'tidak_ada',
            'memerlukan_dokumen' => false,
            'bisa_retroaktif' => false,
        ]);
        
        LeaveType::create([
            'nama_cuti' => 'Ibadah',
            'kuota' => 40, // Durasi dalam hari
            'satuan' => 'hari', // Sama seperti cuti melahirkan
            'periode_reset' => 'tidak_ada',
            'memerlukan_dokumen' => false,
            'bisa_retroaktif' => false,
        ]);

        LeaveType::create([
            'nama_cuti' => 'Sakit',
            'kuota' => 0, // Kuota 0 berarti tidak terbatas
            'satuan' => 'hari',
            'periode_reset' => 'tidak_ada',
            'memerlukan_dokumen' => true, // Wajib upload dokumen
            'bisa_retroaktif' => true, // Bisa input tanggal lampau
        ]);
    }
}
