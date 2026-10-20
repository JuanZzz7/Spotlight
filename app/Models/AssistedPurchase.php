<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssistedPurchase extends Model
{
    public const STATUS_PENDING_REVIEW        = 'pending_review';
    public const STATUS_IN_NEGOTIATION        = 'in_negotiation';
    public const STATUS_PENDING_CONFIRMATION  = 'pending_confirmation';
    public const STATUS_PENDING_PAYMENT       = 'pending_payment';
    public const STATUS_PAYMENT_PROCESSING    = 'payment_processing';
    public const STATUS_PAID                  = 'paid';
    public const STATUS_IN_PREPARATION        = 'in_preparation';
    public const STATUS_SHIPPED               = 'shipped';
    public const STATUS_DELIVERED             = 'delivered';
    public const STATUS_CANCELLED             = 'cancelled';
    public const STATUS_REJECTED              = 'rejected';

    protected $fillable = [
        'request_code',
        'user_id',
        'company_id',
        'product_id',
        'quantity',
        'origin_country',
        'destination_country',
        'destination_state',
        'destination_city',
        'destination_address',
        'destination_postal_code',
        'recipient_name',
        'recipient_phone',
        'recipient_email',
        'original_price',
        'original_currency',
        'shipping_cost_agreed',
        'other_fees_agreed',
        'total_agreed',
        'payment_currency',
        'payment_method_agreed',
        'status',
        'buyer_notes',
        'seller_notes',
        'tracking_number',
        'carrier_name',
        'payment_reference',
        'transaction_id',
        'estimated_delivery_days',
        'paid_at',
        'shipped_at',
        'delivered_at',
    ];

    protected $casts = [
        'quantity'             => 'integer',
        'original_price'       => 'decimal:2',
        'shipping_cost_agreed' => 'decimal:2',
        'other_fees_agreed'    => 'decimal:2',
        'total_agreed'         => 'decimal:2',
        'paid_at'              => 'datetime',
        'shipped_at'           => 'datetime',
        'delivered_at'         => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING_REVIEW       => 'Pendiente de revisión',
            self::STATUS_IN_NEGOTIATION       => 'En negociación',
            self::STATUS_PENDING_CONFIRMATION => 'Pendiente de confirmación',
            self::STATUS_PENDING_PAYMENT      => 'Pendiente de pago',
            self::STATUS_PAYMENT_PROCESSING   => 'Pago en proceso',
            self::STATUS_PAID                 => 'Pagada',
            self::STATUS_IN_PREPARATION       => 'En preparación',
            self::STATUS_SHIPPED              => 'Enviada',
            self::STATUS_DELIVERED            => 'Entregada',
            self::STATUS_CANCELLED            => 'Cancelada',
            self::STATUS_REJECTED             => 'Rechazada',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::getStatuses()[$this->status] ?? ucfirst(str_replace('_', ' ', $this->status));
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING_REVIEW       => 'bg-warning text-dark',
            self::STATUS_IN_NEGOTIATION       => 'bg-info text-dark',
            self::STATUS_PENDING_CONFIRMATION => 'bg-primary text-white',
            self::STATUS_PENDING_PAYMENT      => 'bg-warning text-dark',
            self::STATUS_PAYMENT_PROCESSING   => 'bg-secondary text-white',
            self::STATUS_PAID                 => 'bg-success text-white',
            self::STATUS_IN_PREPARATION       => 'bg-info text-dark',
            self::STATUS_SHIPPED              => 'bg-primary text-white',
            self::STATUS_DELIVERED            => 'bg-success text-white',
            self::STATUS_CANCELLED            => 'bg-dark text-white',
            self::STATUS_REJECTED             => 'bg-danger text-white',
            default                           => 'bg-light text-dark',
        };
    }

    public function getOriginFlagAttribute(): string
    {
        return $this->origin_country === 'MX' ? '🇲🇽' : '🇨🇴';
    }

    public function getDestinationFlagAttribute(): string
    {
        return $this->destination_country === 'MX' ? '🇲🇽' : '🇨🇴';
    }

    public function getFormattedTotalAgreedAttribute(): string
    {
        $amount = (float) $this->total_agreed;
        $currency = $this->payment_currency ?: $this->original_currency;

        if ($currency === 'MXN') {
            return '$ ' . number_format($amount, 2, '.', ',') . ' MXN';
        }
        return '$ ' . number_format($amount, 0, ',', '.') . ' COP';
    }
}
