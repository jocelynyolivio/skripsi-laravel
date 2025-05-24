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
        'notes',
        'purchase_request_detail_id',
    ];

    public function material()
    {
        return $this->belongsTo(DentalMaterial::class);
    }

    public function requestDetail()
    {
        return $this->belongsTo(PurchaseRequestDetail::class, 'purchase_request_detail_id');
    }
}
