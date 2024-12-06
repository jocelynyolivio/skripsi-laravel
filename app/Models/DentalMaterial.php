<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DentalMaterial extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'stock_quantity', 'unit_price'];

    public function procedures()
    {
        return $this->belongsToMany(Procedure::class, 'procedure_materials', 'dental_material_id', 'procedure_id')
                    ->withPivot('quantity');
    }

}