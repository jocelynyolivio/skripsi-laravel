<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id', 'reservation_id', 'teeth_condition', 'treatment', 'odontogram', 'notes', 'date', 'doctor_id'
    ];

    // Relasi dengan pasien
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    // Relasi dengan reservasi
    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    // Relasi dengan dokter
    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function procedures()
    {
        return $this->belongsToMany(Procedure::class, 'medical_record_procedure', 'medical_record_id', 'procedure_id');
    }
    
    public function odontograms()
    {
        return $this->hasMany(Odontogram::class, 'medical_record_id');
    }
    

// Relasi many-to-many dengan DentalMaterial melalui medical_record_dental_material
public function dentalMaterials()
{
    return $this->belongsToMany(DentalMaterial::class, 'medical_record_dental_material')
                ->withPivot('quantity') // Mengambil informasi kuantitas dari pivot
                ->withTimestamps(); // Menambahkan timestamp pada data pivot
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
