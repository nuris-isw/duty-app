<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    protected $guarded = [];
    
    // Casting agar otomatis jadi objek Carbon saat dipanggil
    protected $casts = [
        'date' => 'date',
    ];
}
