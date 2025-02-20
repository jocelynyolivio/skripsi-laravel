<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'doctor_id',
        'procedure_id',
        'quantity',
        'unit_price',
        'total_price',
        'discount',
        'final_price',
        'revenue_percentage',
        'revenue_amount'
    ];
    

    // Relasi ke transaksi
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    // Relasi ke prosedur
    public function procedure()
    {
        return $this->belongsTo(Procedure::class);
    }
}
