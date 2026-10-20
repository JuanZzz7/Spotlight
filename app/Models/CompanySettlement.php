<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanySettlement extends Model
{
    protected $fillable = [
        'company_id',
        'order_id',
        'payment_reference',
        'wompi_transaction_id',
        'gross_amount',
        'shipping_amount',
        'platform_fee',
        'net_payout',
        'status',
        'settled_at',
        'notes',
    ];

    protected $casts = [
        'gross_amount'    => 'decimal:2',
        'shipping_amount' => 'decimal:2',
        'platform_fee'    => 'decimal:2',
        'net_payout'      => 'decimal:2',
        'settled_at'      => 'datetime',
    ];

    public const STATUS_PENDING    = 'pending';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_SETTLED    = 'settled';
    public const STATUS_FAILED     = 'failed';

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
