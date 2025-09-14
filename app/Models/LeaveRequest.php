<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class LeaveRequest extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'leave_type',
        'start_date',
        'end_date',
        'reason',
        'dokumen_pendukung',
        'status',
        'approved_by', 
        'approved_at',
    ];

    /**
     * Mendefinisikan relasi bahwa setiap request dimiliki oleh satu user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi untuk mendapatkan data user yang menyetujui.
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
