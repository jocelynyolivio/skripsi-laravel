<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    // Perbaiki properti fillable agar sesuai dengan nama kolom di migrasi
    protected $fillable = ['patient_id', 'doctor_id', 'tanggal_reservasi', 'jam_mulai', 'jam_selesai', 'status_konfirmasi'];

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }
    
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function medicalRecord()
    {
        return $this->hasOne(MedicalRecord::class);
    }
}
