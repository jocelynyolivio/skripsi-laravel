<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchasePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_invoice_id',
        'coa_id',
        'purchase_amount',
        'total_debt',
        'payment_status',
        'notes'
    ];

    public function purchase_invoice()
    {
        return $this->belongsTo(PurchaseInvoice::class);
    }

    public function coa()
    {
        return $this->belongsTo(ChartOfAccount::class, 'coa_id');
    }
}
