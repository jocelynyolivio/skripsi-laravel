<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Procedure extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

     // Relasi ke Dental Materials (Many to Many)
     public function dentalMaterials()
{
    return $this->belongsToMany(DentalMaterial::class, 'procedure_materials', 'procedure_id', 'dental_material_id')
                ->withPivot('quantity');
}
 
     // Relasi ke Medical Records (Many to Many)
     public function medicalRecords()
     {
         return $this->belongsToMany(MedicalRecord::class, 'medical_record_procedure');
     }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
