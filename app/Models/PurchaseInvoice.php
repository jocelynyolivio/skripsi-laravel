<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseInvoice extends Model
{
    use HasFactory;
    protected $fillable = ['invoice_number', 'supplier_id', 'invoice_date', 'total_amount', 'status', 'purchase_date', 'grand_total','discount','ongkos_kirim'];

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
}
