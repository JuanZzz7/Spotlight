<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ElectronicDocumentItem extends Model
{
    protected $fillable = [
        'electronic_document_id',
        'product_id',
        'line_number',
        'product_code',
        'description',
        'quantity',
        'unit_measure',
        'price_amount',
        'line_extension_amount',
        'discount_amount',
        'tax_type',
        'tax_rate',
        'tax_base_amount',
        'tax_amount',
        'total_item_amount',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'price_amount' => 'decimal:2',
        'line_extension_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_base_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_item_amount' => 'decimal:2',
    ];

    public function document(): BelongsTo
    {
        return $this->belongsTo(ElectronicDocument::class, 'electronic_document_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
