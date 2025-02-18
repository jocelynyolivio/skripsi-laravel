<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pricelist extends Model
{
    use HasFactory;
    public function procedure()
    {
        return $this->belongsTo(Procedure::class, 'procedure_id');
    }
}
