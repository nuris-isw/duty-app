<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
     protected $fillable = [
        'nama_cuti',
        'kuota',
        'satuan',
        'periode_reset',
        'memerlukan_dokumen',
        'bisa_retroaktif',
    ];
}
