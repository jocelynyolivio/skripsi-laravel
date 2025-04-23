<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DentalMaterial extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'unit_type'];

    public function procedures()
    {
        return $this->belongsToMany(Procedure::class, 'procedure_materials', 'dental_material_id', 'procedure_id')
            ->withPivot('quantity');
    }

// app/Models/DentalMaterial.php

public function stockCards()
{
    return $this->hasMany(StockCard::class, 'dental_material_id');
}

public function averageUsage()
{
    return $this->stockCards()
        ->whereNotNull('quantity_out')
        ->avg('quantity_out');
}

public function lastStock()
{
    return $this->stockCards()
        ->orderByDesc('date')
        ->value('remaining_stock');
}

}
