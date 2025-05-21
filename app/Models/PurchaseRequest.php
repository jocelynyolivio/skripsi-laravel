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
        // Semua material dari request
        $requestedMaterials = $this->details->pluck('material_id')->unique()->sort()->values();

        // Semua material dari purchase_order_details yang relasinya ke detail dalam request ini
        $orderedMaterials = \App\Models\PurchaseOrderDetail::whereIn('purchase_request_detail_id', $this->details->pluck('id'))
            ->with('requestDetail')
            ->get()
            ->pluck('requestDetail.material_id')
            ->unique()
            ->sort()
            ->values();

        return $requestedMaterials == $orderedMaterials;
    }
}
