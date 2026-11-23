<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = ['company_id', 'name', 'label', 'icon', 'color_class', 'theme'];

    protected $casts = [
        'theme' => 'array',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }

    public function scopeAvailableForCompany($query, $companyId = null)
    {
        return $query->where(function ($q) use ($companyId) {
            $q->whereNull('company_id');
            if ($companyId) {
                $q->orWhere('company_id', $companyId);
            }
        });
    }

    public function getAccentColorAttribute(): string
    {
        return $this->theme['accent'] ?? '#7F1D3F';
    }
}
