<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receivable extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'coa_id',
        'amount',
        'paid_amount',
        'remaining_amount',
        'due_date',
        'status'
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function coa()
    {
        return $this->belongsTo(ChartOfAccount::class);
    }
}
