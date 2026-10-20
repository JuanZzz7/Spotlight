<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyFiscalProfile extends Model
{
    // Estados del ciclo de vida de habilitación fiscal
    public const STATUS_NOT_REQUESTED = 'not_requested';
    public const STATUS_PENDING_DOCUMENTATION = 'pending_documentation';
    public const STATUS_IN_REVIEW = 'in_review';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_VERIFIED = 'verified';
    public const STATUS_PENDING_TECHNICAL_SETUP = 'pending_technical_setup';
    public const STATUS_ENABLED = 'enabled';
    public const STATUS_SUSPENDED = 'suspended';

    protected $fillable = [
        'company_id',
        'fiscal_status',
        'status_notes',
        'person_type',
        'id_type',
        'identification_number',
        'dv',
        'business_name',
        'commercial_name',
        'tax_regime',
        'tax_responsibilities',
        'ciiu_code',
        'fiscal_address',
        'municipality_code',
        'department_code',
        'postal_code',
        'country_code',
        'fiscal_email',
        'fiscal_phone',
        'rut_document_path',
        'camara_comercio_document_path',
        'legal_rep_id_document_path',
        'submitted_at',
        'verified_at',
        'verified_by_user_id',
        'enabled_at',
    ];

    protected $casts = [
        'tax_responsibilities' => 'array',
        'submitted_at' => 'datetime',
        'verified_at' => 'datetime',
        'enabled_at' => 'datetime',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by_user_id');
    }

    public function isEnabled(): bool
    {
        return $this->fiscal_status === self::STATUS_ENABLED;
    }

    public function isVerified(): bool
    {
        return in_array($this->fiscal_status, [
            self::STATUS_VERIFIED,
            self::STATUS_PENDING_TECHNICAL_SETUP,
            self::STATUS_ENABLED
        ], true);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->fiscal_status) {
            self::STATUS_NOT_REQUESTED => 'No solicitado',
            self::STATUS_PENDING_DOCUMENTATION => 'Documentación pendiente',
            self::STATUS_IN_REVIEW => 'En revisión',
            self::STATUS_REJECTED => 'Rechazado',
            self::STATUS_VERIFIED => 'Verificado legalmente',
            self::STATUS_PENDING_TECHNICAL_SETUP => 'Configuración técnica pendiente',
            self::STATUS_ENABLED => 'Habilitado para emitir',
            self::STATUS_SUSPENDED => 'Suspendido',
            default => ucfirst(str_replace('_', ' ', $this->fiscal_status)),
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->fiscal_status) {
            self::STATUS_ENABLED => 'bg-success text-white',
            self::STATUS_VERIFIED => 'bg-info text-white',
            self::STATUS_PENDING_TECHNICAL_SETUP => 'bg-warning text-dark',
            self::STATUS_IN_REVIEW => 'bg-primary text-white',
            self::STATUS_REJECTED, self::STATUS_SUSPENDED => 'bg-danger text-white',
            default => 'bg-secondary text-white',
        };
    }

    /**
     * Calcula el dígito de verificación DIAN (Módulo 11) a partir de un NIT.
     */
    public static function calculateDv(string $nit): string
    {
        $cleanNit = preg_replace('/\D/', '', $nit);
        if (empty($cleanNit)) {
            return '0';
        }

        $weights = [3, 7, 13, 17, 19, 23, 29, 37, 41, 43, 47, 53, 59, 67, 71];
        $total = 0;
        $len = strlen($cleanNit);

        for ($i = 0; $i < $len; $i++) {
            $digit = (int) $cleanNit[$len - 1 - $i];
            $weight = $weights[$i] ?? 0;
            $total += $digit * $weight;
        }

        $remainder = $total % 11;
        if ($remainder > 1) {
            return (string) (11 - $remainder);
        }

        return (string) $remainder;
    }
}
