<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservation_id',
        'teeth_condition'
    ];

    // Relasi dengan reservasi
    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    // public function procedures()
    // {
    //     return $this->belongsToMany(Procedure::class, 'medical_record_procedure', 'medical_record_id', 'procedure_id');
    // }

    public function odontograms()
    {
        return $this->hasMany(Odontogram::class, 'medical_record_id');
    }

    public function dentalMaterials()
    {
        return $this->belongsToMany(DentalMaterial::class, 'medical_record_dental_material')
            ->withPivot('quantity')
            ->withTimestamps();
    }
    

    public function transaction()
    {
        return $this->hasOne(Transaction::class);
    }


    public function procedureOdontograms()
    {
        return $this->hasMany(ProcedureOdontogram::class);
    }
}
