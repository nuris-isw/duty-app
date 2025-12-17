<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FingerDeviceUser extends Model
{
    // Arahkan ke nama tabel Anda yang unik
    protected $table = 'id_absensfinger';
    
    protected $guarded = [];

    // Relasi ke User Laravel (Pemilik akun)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi ke Log Absensi
    // Kita hubungkan menggunakan 'user_id_machine' bukan 'id'
    public function logs()
    {
        return $this->hasMany(AttendanceLog::class, 'user_id_machine', 'user_id_machine');
    }
}
