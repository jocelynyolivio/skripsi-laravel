<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseInvoice extends Model
{
    use HasFactory;
    protected $fillable = ['invoice_number',  'invoice_date', 'purchase_order_id', 'supplier_id', 'purchase_date', 'payment_requirement', 'received_date', 'due_date', 'status',  'grand_total', 'discount', 'ongkos_kirim'];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function payments()
    {
        return $this->hasMany(PurchasePayment::class);
    }

    public function details()
    {
        return $this->hasMany(PurchaseDetail::class, 'purchase_invoice_id');
    }

    public function latestPayment()
    {
        return $this->hasOne(PurchasePayment::class, 'purchase_invoice_id')->latest();
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }
}
