<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcedureMaterial extends Model
{
    protected $table = 'procedure_materials'; // Specify the table name
    
    // Fields that are mass-assignable
    protected $fillable = ['procedure_id', 'dental_material_id', 'quantity'];

    // Define the relationships
    public function procedure()
    {
        return $this->belongsTo(Procedure::class);
    }

    public function dentalMaterial()
    {
        return $this->belongsTo(DentalMaterial::class, 'dental_material_id');
    }
}
