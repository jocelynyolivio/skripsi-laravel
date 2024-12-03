<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = ['patient_id', 'procedure_id', 'doctor_id', 'date'];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function procedure()
    {
        return $this->belongsTo(Procedure::class);
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function dentalMaterials()
    {
        return $this->belongsToMany(DentalMaterial::class, 'transaction_materials')->withPivot('quantity');
    }
}
