<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role',
        'phone', 'phone_prefix', 'address', 'city', 'state', 'postal_code', 'country', 'birthdate',
        'document', 'avatar', 'active', 'google_id',
        'email_verified_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'password' => 'hashed',
        'active' => 'boolean',
        'birthdate' => 'date',
    ];

    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        return null;
    }

    public function getDisplayNameAttribute(): string
    {
        if ($this->role === 'business' && $this->relationLoaded('company') && $this->company && !empty($this->company->name)) {
            return $this->company->name;
        }
        if ($this->role === 'business' && $this->company()->exists()) {
            return $this->company?->name ?: $this->name;
        }
        return $this->name;
    }

    public function getDisplayInitialAttribute(): string
    {
        $name = $this->display_name;
        $words = preg_split('/\s+/', trim($name));
        if (count($words) >= 2) {
            return mb_strtoupper(mb_substr($words[0], 0, 1) . mb_substr($words[1], 0, 1));
        }
        return mb_strtoupper(mb_substr($name, 0, 2));
    }

    public function getDisplayLogoAttribute(): ?string
    {
        if ($this->role === 'business') {
            $comp = $this->relationLoaded('company') ? $this->company : $this->company()->first();
            if ($comp && !empty($comp->logo)) {
                return asset('storage/' . $comp->logo);
            }
        }
        return $this->avatar_url;
    }

    public function company()
    {
        return $this->hasOne(Company::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function assistedPurchases()
    {
        return $this->hasMany(AssistedPurchase::class);
    }

    public function isAdmin(): bool { return $this->role === 'admin'; }
    public function isBusiness(): bool { return $this->role === 'business'; }
    public function isUser(): bool { return $this->role === 'user'; }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new \App\Notifications\CustomResetPasswordNotification($token));
    }
}

