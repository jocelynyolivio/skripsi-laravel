<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseRequestDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_request_id',
        'dental_material_id',
        'quantity',
        'notes',
    ];

    public function purchaseRequest()
    {
        return $this->belongsTo(PurchaseRequest::class);
    }

    public function material()
    {
        return $this->belongsTo(DentalMaterial::class, 'dental_material_id');
    }

        
}
