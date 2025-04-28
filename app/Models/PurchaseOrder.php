<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    protected $fillable = [
        'order_number',
        'order_date',
        'supplier_id',
        'purchase_request_id',
        'due_date',
        'ship_date',
        'status',
        'shipping_address',
        'payment_requirement',
        'discount',
        'ongkos_kirim',
        'notes',
        'harga_total',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function purchaseRequest()
    {
        return $this->belongsTo(PurchaseRequest::class);
    }

    public function details()
    {
        return $this->hasMany(PurchaseOrderDetail::class);
    }

    public function request()
    {
        return $this->belongsTo(PurchaseRequest::class, 'purchase_request_id');
    }
    public function purchaseInvoices()
    {
        return $this->hasMany(PurchaseInvoice::class);
    }
}
