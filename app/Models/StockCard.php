<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'dental_material_id',
        'date',
        'reference_number',
        'price_in',
        'price_out',
        'quantity_in',
        'quantity_out',
        'remaining_stock',
        'average_price',
        'type',
        'note',
        'updated_by',
    ];

    protected $dates = ['date'];

    /**
     * Relasi ke tabel DentalMaterial.
     */
    public function dentalMaterial()
    {
        return $this->belongsTo(DentalMaterial::class);
    }

    public function editor()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
