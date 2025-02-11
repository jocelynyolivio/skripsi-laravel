<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'medical_record_id',
        'admin_id',
        'amount', // Pastikan 'amount' ada di sini
        'payment_type',
        'payment_status',
    ];


    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
    
    public function medicalRecord()
    {
        return $this->belongsTo(MedicalRecord::class, 'medical_record_id');
    }
}
