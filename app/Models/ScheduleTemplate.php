<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'doctor_id', 'day_of_week', 'start_time', 'end_time', 'is_active',
    ];

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id')->where('role_id', 2);
    }
} 