<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'user_id',
        // Operación multinacional (Colombia / México)
        'country', 'state', 'city', 'postal_code', 'currency', 'phone_prefix', 'tax_id',
        // Vista general
        'name', 'description', 'category', 'logo', 'banner',
        // Aspecto legal
        'tipo_negocio', 'razon_social', 'nit', 'nombre_comercial',
        'cedula_propietario', 'habeas_data_accepted', 'direccion_fisica',
        'camara_comercio_file', 'rut_file', 'rut_personal_file',
        // Redes y contacto
        'address', 'phone', 'email', 'instagram', 'facebook', 'website',
        'opening_time', 'closing_time', 'operating_days',
        'latitude', 'longitude',
        // Datos bancarios y dispersión Wompi
        'bank_name', 'account_type', 'account_number', 'account_holder_name',
        'account_holder_doc_type', 'account_holder_doc_number', 'wompi_merchant_id', 'split_rule_id',
        // Estado
        'status',
    ];

    protected $casts = [
        'operating_days' => 'array',
    ];

    public function user()     { return $this->belongsTo(User::class); }
    public function products() { return $this->hasMany(Product::class); }
    public function expenses() { return $this->hasMany(Expense::class); }
    public function orders()   { return $this->hasMany(Order::class); }
    public function settlements() { return $this->hasMany(CompanySettlement::class); }
    public function assistedPurchases() { return $this->hasMany(AssistedPurchase::class, 'company_id'); }
    public function orderDetails()
    {
        return $this->hasManyThrough(OrderDetail::class, Product::class);
    }

    public function reviews()
    {
        return $this->hasManyThrough(Review::class, Product::class);
    }

    public function getAverageRatingAttribute(): float
    {
        $avg = $this->reviews()->avg('rating');
        return $avg ? round((float) $avg, 1) : 0.0;
    }

    public function getReviewsCountAttribute(): int
    {
        return $this->reviews()->count();
    }

    public function isOpenNow(): bool
    {
        $now = now()->setTimezone('America/Bogota');
        $currentDay = strtolower($now->format('l'));
        $currentTime = $now->format('H:i');

        $days = $this->operating_days;
        if (!is_array($days) || empty($days)) {
            $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        }

        if (!in_array($currentDay, $days)) {
            return false;
        }

        $open = $this->opening_time ? substr($this->opening_time, 0, 5) : '08:00';
        $close = $this->closing_time ? substr($this->closing_time, 0, 5) : '19:00';

        return ($currentTime >= $open && $currentTime <= $close);
    }

    public function getScheduleStatusAttribute(): array
    {
        $isOpen = $this->isOpenNow();
        $open = $this->opening_time ? substr($this->opening_time, 0, 5) : '08:00';
        $close = $this->closing_time ? substr($this->closing_time, 0, 5) : '19:00';

        $openFormatted = date('g:i A', strtotime($open));
        $closeFormatted = date('g:i A', strtotime($close));

        return [
            'is_open' => $isOpen,
            'label'   => $isOpen ? 'Abierto' : 'Cerrado',
            'details' => $isOpen ? "Cierra a las {$closeFormatted}" : "Abre a las {$openFormatted}",
            'hours'   => "{$openFormatted} - {$closeFormatted}",
        ];
    }

    public function getLogoUrlAttribute(): string
    {
        if ($this->logo) {
            return asset('storage/' . $this->logo);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=7F1D3F&color=fff';
    }

    public function fiscalProfile()
    {
        return $this->hasOne(CompanyFiscalProfile::class);
    }

    public function billingConfiguration()
    {
        return $this->hasOne(BillingConfiguration::class);
    }

    public function electronicDocuments()
    {
        return $this->hasMany(ElectronicDocument::class);
    }

    public function billingLogs()
    {
        return $this->hasMany(BillingLog::class);
    }

    public function shipments()
    {
        return $this->hasMany(Shipment::class);
    }

    public function isFormal(): bool
    {
        return $this->tipo_negocio === 'formal';
    }

    public function isInformal(): bool
    {
        return $this->tipo_negocio === 'informal';
    }

    public function isColombian(): bool
    {
        return ($this->country ?? 'CO') === 'CO';
    }

    public function isMexican(): bool
    {
        return ($this->country ?? 'CO') === 'MX';
    }

    public function getCountryNameAttribute(): string
    {
        return $this->isMexican() ? 'México' : 'Colombia';
    }

    public function getCountryFlagAttribute(): string
    {
        return $this->isMexican() ? '🇲🇽' : '🇨🇴';
    }

    public function getCurrencySymbolAttribute(): string
    {
        return ($this->currency ?? 'COP') === 'MXN' ? '$ MXN' : '$ COP';
    }

    public function canIssueElectronicInvoices(): bool
    {
        // La facturación electrónica DIAN está estrictamente reservada para empresas en Colombia
        if (!$this->isColombian()) {
            return false;
        }

        if (!$this->isFormal()) {
            return false;
        }

        return $this->fiscalProfile !== null && $this->fiscalProfile->isEnabled();
    }
}

