<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'company_id', 'country', 'currency', 'name', 'sku', 'description', 'price', 'cost_price', 'stock', 'image', 'gallery', 'active', 'featured',
        'tax_type', 'tax_rate', 'unit_measure', 'standard_code',
    ];

    protected $casts = [
        'active' => 'boolean',
        'featured' => 'boolean',
        'price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'gallery' => 'array',
    ];

    public function company() { return $this->belongsTo(Company::class); }
    public function tags() { return $this->belongsToMany(Tag::class); }
    public function orderDetails() { return $this->hasMany(OrderDetail::class); }
    public function reviews() { return $this->hasMany(Review::class)->latest(); }
    public function assistedPurchases() { return $this->hasMany(AssistedPurchase::class); }

    public function getPrimaryTagAttribute(): ?Tag
    {
        return $this->tags->first();
    }

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

    public const DEFAULT_MXN_TO_COP_RATE = 215.0;

    public static function mxnToCopRate(): float
    {
        $rate = (float) config('app.mxn_to_cop_rate', self::DEFAULT_MXN_TO_COP_RATE);
        return $rate > 0 ? $rate : self::DEFAULT_MXN_TO_COP_RATE;
    }

    public function getFormattedPriceAttribute(): string
    {
        $val = (float) $this->price;
        $hasDecimals = floor($val) != $val;
        if ($this->effective_currency === 'MXN') {
            return '$ ' . number_format($val, 2, '.', ',') . ' MXN';
        }
        return '$ ' . number_format($val, $hasDecimals ? 2 : 0, ',', '.') . ' COP';
    }

    public function getPriceInCopAttribute(): float
    {
        $val = (float) $this->price;
        if ($this->effective_currency === 'MXN' || $this->isMexican()) {
            return round($val * self::mxnToCopRate(), 0);
        }
        return $val;
    }

    public function getFormattedPriceCopAttribute(): string
    {
        $cop = $this->price_in_cop;
        $hasDecimals = floor($cop) != $cop;
        return '$' . number_format($cop, $hasDecimals ? 2 : 0, ',', '.') . ' COP';
    }

    public function getPriceInMxnAttribute(): float
    {
        $val = (float) $this->price;
        if ($this->effective_currency === 'MXN' || $this->isMexican()) {
            return $val;
        }
        return round($val / self::mxnToCopRate(), 2);
    }

    public function getFormattedPriceMxnAttribute(): string
    {
        return '$' . number_format($this->price_in_mxn, 2, ',', '.') . ' MXN';
    }

    protected static array $fileExistsCache = [];

    protected static function cachedStorageExists(?string $path): bool
    {
        if (empty($path)) {
            return false;
        }
        if (!array_key_exists($path, self::$fileExistsCache)) {
            self::$fileExistsCache[$path] = \Illuminate\Support\Facades\Storage::disk('public')->exists($path);
        }
        return self::$fileExistsCache[$path];
    }

    public function getAverageRatingAttribute(): float
    {
        if (array_key_exists('reviews_avg_rating', $this->attributes)) {
            $avg = $this->attributes['reviews_avg_rating'];
            return $avg ? round((float) $avg, 1) : 0.0;
        }
        if ($this->relationLoaded('reviews')) {
            $avg = $this->reviews->avg('rating');
            return $avg ? round((float) $avg, 1) : 0.0;
        }
        $avg = $this->reviews()->avg('rating');
        return $avg ? round((float) $avg, 1) : 0.0;
    }

    public function getReviewsCountAttribute(): int
    {
        if (array_key_exists('reviews_count', $this->attributes)) {
            return (int) $this->attributes['reviews_count'];
        }
        if ($this->relationLoaded('reviews')) {
            return $this->reviews->count();
        }
        return $this->reviews()->count();
    }

    public function getRatingStarsArrayAttribute(): array
    {
        $total = $this->reviews_count;
        $countsByStar = [];

        if ($this->relationLoaded('reviews')) {
            foreach ($this->reviews as $rev) {
                $r = (int) $rev->rating;
                $countsByStar[$r] = ($countsByStar[$r] ?? 0) + 1;
            }
        } elseif ($total > 0) {
            $countsByStar = $this->reviews()
                ->selectRaw('rating, COUNT(*) as aggregate')
                ->groupBy('rating')
                ->pluck('aggregate', 'rating')
                ->toArray();
        }

        $breakdown = [];
        for ($star = 5; $star >= 1; $star--) {
            $count = (int) ($countsByStar[$star] ?? 0);
            $percentage = $total > 0 ? round(($count / $total) * 100) : 0;
            $breakdown[$star] = [
                'count'      => $count,
                'percentage' => $percentage,
            ];
        }
        return $breakdown;
    }

    public function hasCustomImage(): bool
    {
        return self::cachedStorageExists($this->image);
    }

    public function getPlaceholderIconAttribute(): string
    {
        if ($this->relationLoaded('tags') && $this->tags->isNotEmpty() && !empty($this->tags->first()->icon)) {
            return $this->tags->first()->icon;
        }

        $haystack = mb_strtolower(($this->name ?? '') . ' ' . ($this->category ?? '') . ' ' . ($this->description ?? ''));

        return match (true) {
            str_contains($haystack, 'audífono') || str_contains($haystack, 'audifono') || str_contains($haystack, 'audio') => 'bi-headphones',
            str_contains($haystack, 'mouse') || str_contains($haystack, 'ratón') => 'bi-mouse2-fill',
            str_contains($haystack, 'teclado') || str_contains($haystack, 'keyboard') => 'bi-keyboard-fill',
            str_contains($haystack, 'bolso') || str_contains($haystack, 'cartera') || str_contains($haystack, 'mochila') || str_contains($haystack, 'cuero') => 'bi-handbag-fill',
            str_contains($haystack, 'camiseta') || str_contains($haystack, 'camisa') || str_contains($haystack, 'ruana') || str_contains($haystack, 'ropa') || str_contains($haystack, 'moda') || str_contains($haystack, 'prenda') => 'bi-tag-fill',
            str_contains($haystack, 'café') || str_contains($haystack, 'cafe') || str_contains($haystack, 'bebida') => 'bi-cup-hot-fill',
            str_contains($haystack, 'queso') || str_contains($haystack, 'lácteo') || str_contains($haystack, 'lacteo') || str_contains($haystack, 'comida') || str_contains($haystack, 'alimento') => 'bi-basket2-fill',
            str_contains($haystack, 'computador') || str_contains($haystack, 'portátil') || str_contains($haystack, 'laptop') || str_contains($haystack, 'tecnolog') => 'bi-laptop',
            str_contains($haystack, 'artesan') || str_contains($haystack, 'cerámica') || str_contains($haystack, 'tejido') => 'bi-palette-fill',
            default => 'bi-bag-heart-fill',
        };
    }

    public function getImageUrlAttribute()
    {
        if (self::cachedStorageExists($this->image)) {
            return asset('storage/' . $this->image);
        }
        if ($this->company && self::cachedStorageExists($this->company->logo)) {
            return asset('storage/' . $this->company->logo);
        }
        return asset('images/logo-symbol-burgundy.png');
    }

    public function getGalleryUrlsAttribute(): array
    {
        $urls = [];
        if (is_array($this->gallery)) {
            foreach ($this->gallery as $path) {
                if (self::cachedStorageExists($path)) {
                    $urls[] = asset('storage/' . $path);
                }
            }
        }
        return $urls;
    }

    public function getAllImagesAttribute(): array
    {
        $images = [];
        if (self::cachedStorageExists($this->image)) {
            $images[] = asset('storage/' . $this->image);
        }
        foreach ($this->gallery_urls as $url) {
            if (!in_array($url, $images)) {
                $images[] = $url;
            }
        }
        if (empty($images)) {
            $images[] = $this->image_url;
        }
        return $images;
    }
}
