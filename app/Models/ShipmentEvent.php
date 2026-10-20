<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShipmentEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipment_id',
        'status',
        'title',
        'description',
        'location',
        'occurred_at',
    ];

    protected $casts = [
        'occurred_at' => 'datetime',
    ];

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    public function getIconAttribute(): string
    {
        return match ($this->status) {
            'order_confirmed', 'pending' => 'bi-receipt',
            'preparing'                  => 'bi-box-seam',
            'dispatched'                 => 'bi-truck',
            'in_transit'                 => 'bi-arrow-left-right',
            'arrival_hub'                => 'bi-buildings',
            'out_for_delivery'           => 'bi-bicycle',
            'delivered'                  => 'bi-check-circle-fill',
            'failed'                     => 'bi-exclamation-triangle-fill',
            default                      => 'bi-geo-alt-fill',
        };
    }
}
