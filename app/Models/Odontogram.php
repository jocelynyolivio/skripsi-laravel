<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Odontogram extends Model
{
    use HasFactory;

    protected $fillable = ['patient_id', 'medical_record_id', 'tooth_number', 'condition', 'notes', 'surface'];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function medicalRecord()
    {
        return $this->belongsTo(MedicalRecord::class);
    }
    
    public function procedures()
    {
        return $this->belongsToMany(Procedure::class, 'medical_record_procedure', 'tooth_number', 'procedure_id')
            ->withPivot('surface');
    }
}
