<?php

namespace App\Models;

use App\Models\TransactionItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'medical_record_id',
        'admin_id',
        'total_amount',
        'payment_method'
    ];

    // Relasi ke User (yang melakukan transaksi)
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    // Relasi ke Admin (yang mencatat transaksi)
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    // Relasi ke Rekam Medis (jika transaksi terkait rekam medis)
    public function medicalRecord()
    {
        return $this->belongsTo(MedicalRecord::class);
    }

    // Relasi ke transaction_items
    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function getRemainingAmountAttribute()
    {
        $totalPayments = $this->payments()->sum('amount');
        return $this->total_amount - $totalPayments;
    }

    public function getPaymentStatusAttribute()
    {
        return $this->remaining_amount > 0 ? 'belum lunas' : 'lunas';
    }
}
