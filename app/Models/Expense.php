<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'expense_date',
        'created_by',
        'coa_out',
        'coa_in',
        'amount',
        'description',
        'reference_number',
        'supplier_id',
        'payment_method'
    ];
    
    public function admin()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function coaOut()
    {
        return $this->belongsTo(ChartOfAccount::class, 'coa_out');
    }

    public function coaIn()
    {
        return $this->belongsTo(ChartOfAccount::class, 'coa_in');
    }
}
