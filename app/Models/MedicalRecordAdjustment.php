<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MedicalRecordAdjustment extends Model
{
    use HasFactory;

    protected $fillable = [
        'medical_record_id',
        'notes',
        'adjusted_by',
        'adjusted_at',
    ];

    protected $dates = ['adjusted_at'];

    public function medicalRecord()
    {
        return $this->belongsTo(MedicalRecord::class);
    }

    public function adjustedBy()
    {
        return $this->belongsTo(User::class, 'adjusted_by');
    }
}

