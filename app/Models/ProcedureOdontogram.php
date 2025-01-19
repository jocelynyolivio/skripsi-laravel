<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcedureOdontogram extends Model
{
    use HasFactory;

    protected $table = 'procedure_odontogram';

    protected $fillable = [
        'medical_record_id',
        'procedure_id',
        'tooth_number',
        'notes'
    ];

    public function medicalRecord()
    {
        return $this->belongsTo(MedicalRecord::class);
    }

    public function procedure()
    {
        return $this->belongsTo(Procedure::class);
    }
}
