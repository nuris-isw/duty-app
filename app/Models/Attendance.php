<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    // Pastikan mengarah ke tabel yang benar
    protected $table = 'attendances';

    // Kolom yang boleh diisi secara massal (create/update)
    protected $fillable = [
        'user_id',
        'date',
        'clock_in',
        'clock_out',
        'status', // present, late, absent, leave, sick, dll
        'note',   // Catatan (misal: "Terlambat", "Pulang Cepat", "Koreksi")
    ];

    // Casting tipe data agar mudah diolah Carbon
    protected $casts = [
        'date' => 'date',
        // Kita biarkan clock_in/out sebagai string (format H:i:s) 
        // agar tidak otomatis diberi tanggal hari ini oleh Laravel
    ];

    /**
     * Relasi ke User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
