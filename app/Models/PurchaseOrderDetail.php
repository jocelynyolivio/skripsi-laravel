<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderDetail extends Model
{
    protected $fillable = [
        'purchase_order_id',
        'material_id',
        'quantity',
        'price',
        'notes'
    ];

    public function material()
    {
        return $this->belongsTo(DentalMaterial::class);
    }
}