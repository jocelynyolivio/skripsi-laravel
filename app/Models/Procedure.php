<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Procedure extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'requires_tooth'];

    // Relasi ke Dental Materials (Many to Many)
    public function dentalMaterials()
    {
        return $this->belongsToMany(DentalMaterial::class, 'procedure_materials', 'procedure_id', 'dental_material_id')
            ->withPivot('quantity');
    }

     // Relasi ke Medical Records (Many to Many)
     public function medicalRecords()
     {
         return $this->belongsToMany(MedicalRecord::class, 'medical_record_procedure')->withPivot('tooth_number','notes');
     }

     public function pricelists()
     {
         return $this->hasMany(Pricelist::class, 'procedure_id');
     }
 
     // Mengambil harga terbaru berdasarkan `effective_date`
     public function latestPrice()
     {
         return $this->hasOne(Pricelist::class, 'procedure_id')->latest('effective_date');
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

    public function odontograms()
    {
        return $this->belongsToMany(Odontogram::class, 'medical_record_procedure', 'procedure_id', 'odontogram_id')
            ->withTimestamps();
    }
}
