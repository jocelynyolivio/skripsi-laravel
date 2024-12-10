<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'date', 'amount', 'category_id', 'description', 
        'created_by', 'dental_material_id', 'quantity'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function dentalMaterial()
    {
        return $this->belongsTo(DentalMaterial::class, 'dental_material_id');
    }
}
