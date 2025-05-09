<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'tanggal_reservasi',
        'jam_mulai',
        'jam_selesai',
        'status_konfirmasi',
        'teeth_condition',
        'updated_by',
        'subjective',
        'objective',
        'assessment',
        'plan'
    ];

    public function procedures()
    {
        return $this->belongsToMany(Procedure::class, 'medical_record_procedure', 'medical_record_id', 'procedure_id')
            ->withPivot('tooth_number', 'notes');
    }


    public function odontograms()
    {
        return $this->hasMany(Odontogram::class, 'medical_record_id');
    }


    public function transaction()
    {
        return $this->hasOne(Transaction::class);
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function editor()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function adjustments()
    {
        return $this->hasMany(MedicalRecordAdjustment::class);
    }
}
