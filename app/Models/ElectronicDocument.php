<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ElectronicDocument extends Model
{
    public const TYPE_INVOICE = '01';
    public const TYPE_CREDIT_NOTE = '91';
    public const TYPE_DEBIT_NOTE = '92';

    public const STATUS_DRAFT = 'draft';
    public const STATUS_SENT = 'sent';
    public const STATUS_VALIDATED = 'validated';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_ERROR = 'error';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'company_id',
        'order_id',
        'user_id',
        'document_type',
        'parent_document_id',
        'correction_concept_code',
        'correction_reason',
        'prefix',
        'consecutive',
        'full_number',
        'uuid_cufe',
        'issue_date',
        'issue_time',
        'due_date',
        'environment',
        'issuer_nit',
        'issuer_dv',
        'issuer_business_name',
        'issuer_tax_regime',
        'issuer_address',
        'issuer_phone',
        'issuer_email',
        'issuer_municipality_code',
        'customer_id_type',
        'customer_id_number',
        'customer_dv',
        'customer_name',
        'customer_email',
        'customer_phone',
        'customer_address',
        'customer_municipality_code',
        'line_extension_amount',
        'tax_exclusive_amount',
        'tax_inclusive_amount',
        'allowance_total_amount',
        'tax_total_amount',
        'payable_amount',
        'payment_method_code',
        'payment_means_code',
        'status',
        'dian_status',
        'dian_response_message',
        'dian_validation_errors',
        'xml_path',
        'pdf_path',
        'qr_content',
        'provider_track_id',
        'provider_raw_response',
        'sent_at',
        'validated_at',
        'delivered_to_customer_at',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'sent_at' => 'datetime',
        'validated_at' => 'datetime',
        'delivered_to_customer_at' => 'datetime',
        'dian_validation_errors' => 'array',
        'provider_raw_response' => 'array',
        'line_extension_amount' => 'decimal:2',
        'tax_exclusive_amount' => 'decimal:2',
        'tax_inclusive_amount' => 'decimal:2',
        'allowance_total_amount' => 'decimal:2',
        'tax_total_amount' => 'decimal:2',
        'payable_amount' => 'decimal:2',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parentDocument(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_document_id');
    }

    public function creditNotes(): HasMany
    {
        return $this->hasMany(self::class, 'parent_document_id')
            ->where('document_type', self::TYPE_CREDIT_NOTE);
    }

    public function debitNotes(): HasMany
    {
        return $this->hasMany(self::class, 'parent_document_id')
            ->where('document_type', self::TYPE_DEBIT_NOTE);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ElectronicDocumentItem::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(BillingLog::class);
    }

    public function isValidated(): bool
    {
        return $this->status === self::STATUS_VALIDATED;
    }

    public function isInvoice(): bool
    {
        return $this->document_type === self::TYPE_INVOICE;
    }

    public function isCreditNote(): bool
    {
        return $this->document_type === self::TYPE_CREDIT_NOTE;
    }

    public function isDebitNote(): bool
    {
        return $this->document_type === self::TYPE_DEBIT_NOTE;
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->document_type) {
            self::TYPE_INVOICE => 'Factura Electrónica de Venta',
            self::TYPE_CREDIT_NOTE => 'Nota Crédito Electrónica',
            self::TYPE_DEBIT_NOTE => 'Nota Débito Electrónica',
            default => 'Documento Electrónico',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT => 'Borrador',
            self::STATUS_SENT => 'Enviado a DIAN',
            self::STATUS_VALIDATED => 'Validado DIAN',
            self::STATUS_REJECTED => 'Rechazado DIAN',
            self::STATUS_ERROR => 'Error de Transmisión',
            self::STATUS_CANCELLED => 'Anulado',
            default => ucfirst($this->status),
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_VALIDATED => 'bg-success text-white',
            self::STATUS_SENT => 'bg-info text-white',
            self::STATUS_REJECTED, self::STATUS_ERROR => 'bg-danger text-white',
            self::STATUS_CANCELLED => 'bg-dark text-white',
            default => 'bg-secondary text-white',
        };
    }
}
