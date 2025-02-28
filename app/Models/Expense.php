<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'amount',
        'category_id',
        'supplier_id',
        'description',
        'expired_at',
        'created_by',
        'dental_material_id',
        'quantity',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function dentalMaterial()
    {
        return $this->belongsTo(DentalMaterial::class, 'dental_material_id');
    }

    // App\Models\Expense.php
    public function admin()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // app/Models/Expense.php

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class, 'expense_id');
    }
}
