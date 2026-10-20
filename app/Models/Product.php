<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'company_id', 'country', 'currency', 'name', 'sku', 'description', 'price', 'cost_price', 'stock', 'image', 'active', 'featured',
        'tax_type', 'tax_rate', 'unit_measure', 'standard_code',
    ];

    protected $casts = [
        'active' => 'boolean',
        'featured' => 'boolean',
        'price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'tax_rate' => 'decimal:2',
    ];

    public function company() { return $this->belongsTo(Company::class); }
    public function orderDetails() { return $this->hasMany(OrderDetail::class); }
    public function reviews() { return $this->hasMany(Review::class)->latest(); }
    public function assistedPurchases() { return $this->hasMany(AssistedPurchase::class); }

    public function isMexican(): bool
    {
        return ($this->country ?: ($this->company?->country ?: 'CO')) === 'MX';
    }

    public function isColombian(): bool
    {
        return !$this->isMexican();
    }

    public function getEffectiveCurrencyAttribute(): string
    {
        return $this->currency ?: ($this->company?->currency ?: 'COP');
    }

    public function getCountryFlagAttribute(): string
    {
        return $this->isMexican() ? '🇲🇽' : '🇨🇴';
    }

    public function getCountryNameAttribute(): string
    {
        return $this->isMexican() ? 'México' : 'Colombia';
    }

    public function getFormattedPriceAttribute(): string
    {
        if ($this->effective_currency === 'MXN') {
            return '$ ' . number_format((float) $this->price, 2, '.', ',') . ' MXN';
        }
        return '$ ' . number_format((float) $this->price, 0, ',', '.') . ' COP';
    }

    public function getAverageRatingAttribute(): float
    {
        $avg = $this->reviews()->avg('rating');
        return $avg ? round((float)$avg, 1) : 0.0;
    }

    public function getReviewsCountAttribute(): int
    {
        return $this->reviews()->count();
    }

    public function getRatingStarsArrayAttribute(): array
    {
        $total = $this->reviews_count;
        $breakdown = [];
        for ($star = 5; $star >= 1; $star--) {
            $count = $this->reviews()->where('rating', $star)->count();
            $percentage = $total > 0 ? round(($count / $total) * 100) : 0;
            $breakdown[$star] = [
                'count' => $count,
                'percentage' => $percentage,
            ];
        }
        return $breakdown;
    }

    public function getImageUrlAttribute()
    {
        if (!empty($this->image) && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->image)) {
            return asset('storage/' . $this->image);
        }
        if ($this->company && !empty($this->company->logo) && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->company->logo)) {
            return asset('storage/' . $this->company->logo);
        }
        return asset('images/logo.png');
    }
}
