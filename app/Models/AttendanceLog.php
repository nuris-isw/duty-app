<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceLog extends Model
{
    protected $table = 'raw_attendance_logs';
    protected $guarded = [];
    
    // Screenshot menunjukkan ada created_at tapi tidak ada updated_at
    public $timestamps = false;

    // Casting agar timestamp otomatis jadi Carbon object (mudah diolah tanggal/jamnya)
    protected $casts = [
        'timestamp' => 'datetime',
    ];

    // Relasi balik ke User Mesin
    public function deviceUser()
    {
        return $this->belongsTo(FingerDeviceUser::class, 'user_id_machine', 'user_id_machine');
    }
    
    /**
     * Helper untuk menerjemahkan kode Punch dari mesin (Opsional, sesuaikan dengan mesin Anda)
     */
    public function getPunchTypeAttribute()
    {
        // Contoh logika umum mesin finger:
        // 0/CheckIn, 1/CheckOut. Sesuaikan dengan dokumentasi mesin Anda.
        return match ($this->punch) {
            0 => 'Masuk',
            1 => 'Pulang',
            default => 'Log Lain',
        };
    }
}
