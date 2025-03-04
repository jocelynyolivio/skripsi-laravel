<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_invoice_id', 'dental_material_id', 'quantity', 'unit', 'unit_price', 'subtotal'
    ];

    public function purchaseInvoice()
    {
        return $this->belongsTo(PurchaseInvoice::class);
    }

    public function material()
    {
        return $this->belongsTo(DentalMaterial::class, 'dental_material_id');
    }
}
