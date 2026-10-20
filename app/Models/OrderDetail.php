<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderDetail extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'company_id',
        'product_name',
        'product_sku',
        'quantity',
        'unit_price',
        'subtotal',
        'tax_type',
        'tax_rate',
        'tax_amount',
        'discount_amount',
        'platform_fee',
        'company_payout',
    ];

    protected $casts = [
        'unit_price'      => 'decimal:2',
        'subtotal'        => 'decimal:2',
        'tax_rate'        => 'decimal:2',
        'tax_amount'      => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'platform_fee'    => 'decimal:2',
        'company_payout'  => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Devuelve el nombre del producto preservando el snapshot histórico si el producto original fue editado o borrado.
     */
    public function getEffectiveProductNameAttribute(): string
    {
        return $this->product_name ?: ($this->product ? $this->product->name : 'Producto sin referencia');
    }
}
