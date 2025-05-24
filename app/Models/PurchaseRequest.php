<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_number',
        'request_date',
        'requested_by',
        'status',
        'notes',
        'approved_by',
        'approved_at',
        'approval_notes',
        'updated_by'
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];


    public function details()
    {
        return $this->hasMany(PurchaseRequestDetail::class);
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function editor()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function isFullyOrdered()
    {
        // dd('masuk fully ordered');
        // Material yang ada di request
        $requestedMaterials = $this->details->pluck('dental_material_id')->unique()->sort()->values();
        // dd($requestedMaterials);

        // Material yang sudah pernah diorder (jenis saja)
        $orderedMaterials = PurchaseOrderDetail::whereHas('requestDetail', function ($q) {
            $q->where('purchase_request_id', $this->id);
        })
            ->with('requestDetail')
            ->get()
            ->pluck('requestDetail.dental_material_id')
            ->unique()
            ->sort()
            ->values();

        // dd($orderedMaterials);

        return $requestedMaterials->diff($orderedMaterials)->isEmpty();
    }
}
