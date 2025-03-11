<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryCalculation extends Model
{
    use HasFactory;

    protected $table = 'salary_calculations';

    protected $fillable = [
        'user_id',
        'month',
        'normal_shift',
        'holiday_shift',
        'shift_pagi',
        'shift_siang',
        'lembur',
        'base_salary',
        'allowance',
        'grand_total',
        'adjustment',
        'adjustment_notes'
    ];

    /**
     * Relasi ke tabel Users
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Scope untuk filter berdasarkan bulan dan tahun
     */
    public function scopeForMonthYear($query, $month, $year)
    {
        return $query->where('month', "$year-$month");
    }

    /**
     * Format tampilan uang
     */
    public function getFormattedSalaryAttribute()
    {
        return number_format($this->grand_total, 2, ',', '.');
    }
}

