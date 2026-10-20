<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    public const PAYMENT_PENDING    = 'pending';
    public const PAYMENT_PROCESSING = 'processing';
    public const PAYMENT_APPROVED   = 'approved';
    public const PAYMENT_DECLINED   = 'declined';
    public const PAYMENT_VOIDED     = 'voided';
    public const PAYMENT_ERROR      = 'error';

    public const ORDER_PENDING_PAYMENT = 'pending_payment';
    public const ORDER_CONFIRMED       = 'confirmed';
    public const ORDER_IN_PREPARATION  = 'in_preparation';
    public const ORDER_SHIPPED         = 'shipped';
    public const ORDER_DELIVERED       = 'delivered';
    public const ORDER_CANCELLED       = 'cancelled';

    public const SETTLEMENT_PENDING    = 'pending';
    public const SETTLEMENT_PROCESSING = 'processing';
    public const SETTLEMENT_SETTLED    = 'settled';
    public const SETTLEMENT_FAILED     = 'failed';

    protected $fillable = [
        'user_id',
        'company_id',
        'country',
        'currency',
        'payment_gateway',
        'reference',
        'payment_reference',
        'transaction_id',
        'payment_method',
        'payment_status',
        'order_status',
        'shipping_address',
        'total',
        'subtotal_company',
        'shipping_cost_company',
        'platform_fee',
        'net_company_amount',
        'settlement_status',
        'total_company_settlement',
        'status',
        'billing_status',
        'payment_method_code',
        'payment_due_date',
    ];

    protected $casts = [
        'total'                    => 'decimal:2',
        'subtotal_company'         => 'decimal:2',
        'shipping_cost_company'    => 'decimal:2',
        'platform_fee'             => 'decimal:2',
        'net_company_amount'       => 'decimal:2',
        'total_company_settlement' => 'decimal:2',
        'shipping_address'         => 'array',
        'payment_due_date'         => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function settlement(): HasOne
    {
        return $this->hasOne(CompanySettlement::class);
    }

    public function paymentLogs(): HasMany
    {
        return $this->hasMany(PaymentLog::class);
    }

    public function electronicDocuments(): HasMany
    {
        return $this->hasMany(ElectronicDocument::class);
    }

    public function electronicDocument(): HasOne
    {
        return $this->hasOne(ElectronicDocument::class)
            ->where('document_type', ElectronicDocument::TYPE_INVOICE)
            ->latestOfMany();
    }

    public function shipment(): HasOne
    {
        return $this->hasOne(Shipment::class)->latestOfMany();
    }

    public function isBilled(): bool
    {
        return $this->billing_status === 'billed' || ($this->electronicDocument && $this->electronicDocument->isValidated());
    }

    public function canBeBilled(): bool
    {
        return !$this->isBilled() && $this->status !== 'cancelled' && $this->order_status !== 'cancelled';
    }

    public function isPaid(): bool
    {
        return $this->payment_status === self::PAYMENT_APPROVED || $this->status === 'paid';
    }

    public function scopeByPaymentReference($query, string $ref)
    {
        return $query->where('payment_reference', $ref)->orWhere('reference', $ref);
    }
}
