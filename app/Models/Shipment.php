<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'company_id',
        'tracking_number',
        'carrier',
        'carrier_name',
        'status',
        'origin_city',
        'destination_city',
        'destination_address',
        'recipient_name',
        'recipient_phone',
        'current_location',
        'estimated_delivery_at',
        'shipped_at',
        'delivered_at',
        'notes',
    ];

    protected $casts = [
        'estimated_delivery_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public const STATUS_PENDING          = 'pending';
    public const STATUS_PREPARING        = 'preparing';
    public const STATUS_DISPATCHED       = 'dispatched';
    public const STATUS_IN_TRANSIT       = 'in_transit';
    public const STATUS_OUT_FOR_DELIVERY = 'out_for_delivery';
    public const STATUS_DELIVERED        = 'delivered';
    public const STATUS_FAILED           = 'failed';

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(ShipmentEvent::class)->orderBy('occurred_at', 'desc');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING          => 'Pendiente',
            self::STATUS_PREPARING        => 'En Preparación y Empaque',
            self::STATUS_DISPATCHED       => 'Despachado con Transportadora',
            self::STATUS_IN_TRANSIT       => 'En Tránsito hacia Destino',
            self::STATUS_OUT_FOR_DELIVERY => 'En Ruta de Entrega (Última Milla)',
            self::STATUS_DELIVERED        => 'Entregado con Éxito',
            self::STATUS_FAILED           => 'Novedad en el Envío',
            default                       => ucfirst($this->status),
        };
    }

    public function getProgressPercentageAttribute(): int
    {
        return match ($this->status) {
            self::STATUS_PENDING          => 12,
            self::STATUS_PREPARING        => 28,
            self::STATUS_DISPATCHED       => 50,
            self::STATUS_IN_TRANSIT       => 72,
            self::STATUS_OUT_FOR_DELIVERY => 90,
            self::STATUS_DELIVERED        => 100,
            self::STATUS_FAILED           => 40,
            default                       => 20,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING          => '#64748b',
            self::STATUS_PREPARING        => '#f59e0b',
            self::STATUS_DISPATCHED       => '#3b82f6',
            self::STATUS_IN_TRANSIT       => '#8b5cf6',
            self::STATUS_OUT_FOR_DELIVERY => '#06b6d4',
            self::STATUS_DELIVERED        => '#10b981',
            self::STATUS_FAILED           => '#ef4444',
            default                       => '#64748b',
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING          => 'bg-secondary',
            self::STATUS_PREPARING        => 'bg-warning text-dark',
            self::STATUS_DISPATCHED       => 'bg-primary text-white',
            self::STATUS_IN_TRANSIT       => 'bg-indigo text-white',
            self::STATUS_OUT_FOR_DELIVERY => 'bg-info text-dark',
            self::STATUS_DELIVERED        => 'bg-success text-white',
            self::STATUS_FAILED           => 'bg-danger text-white',
            default                       => 'bg-secondary text-white',
        };
    }

    public function isDelivered(): bool
    {
        return $this->status === self::STATUS_DELIVERED;
    }
}
