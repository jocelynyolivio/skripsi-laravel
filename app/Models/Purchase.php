<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'expense_id',
        'coa_id',
        'purchase_amount',
        'total_debt',
        'payment_status',
        'notes'
    ];

    public function expense()
    {
        return $this->belongsTo(Expense::class);
    }

    public function coa()
    {
        return $this->belongsTo(ChartOfAccount::class, 'coa_id');
    }
}
