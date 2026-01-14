<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceCorrectionRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'proposed_start_time',
        'proposed_end_time',
        'reason',
        'dokumen_pendukung',
        'status',
        'approved_by',
        'approved_at',
        'rejection_reason',
    ];

    // Relasi ke Pemohon
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi ke Penyetuju (Atasan)
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
