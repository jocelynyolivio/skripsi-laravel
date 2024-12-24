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

     public function priceLists()
{
    return $this->hasMany(Pricelist::class);
}

// Mengambil harga dasar (non-promosi)
public function basePrice()
{
    return $this->hasOne(Pricelist::class)->where('is_promo', false)->latestOfMany('effective_date');
}

// Mengambil harga promosi terbaru jika ada
public function promoPrice()
{
    return $this->hasOne(Pricelist::class)->where('is_promo', true)->latestOfMany('effective_date');
}

}
