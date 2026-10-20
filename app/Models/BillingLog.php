<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BillingLog extends Model
{
    protected $fillable = [
        'company_id',
        'electronic_document_id',
        'action',
        'provider',
        'endpoint',
        'idempotency_key',
        'request_payload',
        'response_payload',
        'http_status',
        'is_success',
        'error_message',
        'ip_address',
    ];

    protected $casts = [
        'request_payload' => 'array',
        'response_payload' => 'array',
        'is_success' => 'boolean',
        'http_status' => 'integer',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(ElectronicDocument::class, 'electronic_document_id');
    }
}
