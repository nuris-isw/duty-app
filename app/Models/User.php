<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\LeaveRequest;
use App\Models\UserLeaveQuota;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id', // <-- WAJIB ADA
        'avatar',    // <-- WAJIB ADA
        'email_verified_at', // <-- WAJIB ADA
        'role', // <-- Tambahan
        'atasan_id', // <-- Tambahan
        'signature', // <-- Tambahan
        'unit_kerja_id',
        'jabatan_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function leaveRequests()
    {
        return $this->hasMany(leaveRequest::class);
    }

    /**
     * Relasi untuk mendapatkan data atasan dari seorang pegawai.
     */
    public function superior()
    {
        return $this->belongsTo(User::class, 'atasan_id');
    }

    /**
     * Relasi untuk mendapatkan semua bawahan dari seorang atasan.
     */
    public function subordinates()
    {
        return $this->hasMany(User::class, 'atasan_id');
    }

    public function unitKerja()
    {
        return $this->belongsTo(UnitKerja::class);
    }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class);
    }

    /**
     * Mendefinisikan relasi one-to-many ke UserLeaveQuota.
     */
    public function userLeaveQuotas()
    {
        return $this->hasMany(UserLeaveQuota::class);
    }
}
