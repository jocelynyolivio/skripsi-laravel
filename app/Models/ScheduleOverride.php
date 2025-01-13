<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleOverride extends Model
{
    use HasFactory;

    protected $fillable = [
        'doctor_id', 'override_date', 'start_time', 'end_time', 'is_available', 'reason',
    ];

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }
} 