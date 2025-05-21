<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JournalEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'purchase_id',
        'entry_date',
        'description'
    ];

    public function details()
    {
        return $this->hasMany(JournalDetail::class, 'journal_entry_id');
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }

    public function purchaseinvoice()
    {
        return $this->belongsTo(PurchaseInvoice::class, 'purchase_id');
    }

    
}
