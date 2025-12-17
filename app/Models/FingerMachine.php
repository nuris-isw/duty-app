<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FingerMachine extends Model
{
    protected $table = 'finger_machines';
    protected $guarded = [];
    
    // Karena di screenshot Anda tidak ada updated_at
    public $timestamps = false;
}
