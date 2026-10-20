<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BillingConfiguration extends Model
{
    protected $fillable = [
        'company_id',
        'provider',
        'environment',
        'api_token',
        'api_secret',
        'software_id',
        'software_pin',
        'test_set_id',
        'resolution_number',
        'resolution_prefix',
        'range_from',
        'range_to',
        'current_consecutive',
        'technical_key',
        'resolution_date_from',
        'resolution_date_to',
        'credit_note_prefix',
        'credit_note_consecutive',
        'debit_note_prefix',
        'debit_note_consecutive',
        'is_connected',
        'last_connection_test_at',
        'last_connection_error',
    ];

    protected $casts = [
        'api_token' => 'encrypted',
        'api_secret' => 'encrypted',
        'is_connected' => 'boolean',
        'last_connection_test_at' => 'datetime',
        'resolution_date_from' => 'date',
        'resolution_date_to' => 'date',
        'range_from' => 'integer',
        'range_to' => 'integer',
        'current_consecutive' => 'integer',
        'credit_note_consecutive' => 'integer',
        'debit_note_consecutive' => 'integer',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Obtiene el siguiente consecutivo formateado para factura electrónica.
     */
    public function getNextInvoiceNumber(): string
    {
        $prefix = trim((string) $this->resolution_prefix);
        $consecutive = $this->current_consecutive;
        return $prefix ? "{$prefix}-{$consecutive}" : (string) $consecutive;
    }

    /**
     * Obtiene el siguiente consecutivo formateado para nota crédito.
     */
    public function getNextCreditNoteNumber(): string
    {
        $prefix = trim((string) $this->credit_note_prefix) ?: 'NC';
        $consecutive = $this->credit_note_consecutive;
        return "{$prefix}-{$consecutive}";
    }

    /**
     * Obtiene el siguiente consecutivo formateado para nota débito.
     */
    public function getNextDebitNoteNumber(): string
    {
        $prefix = trim((string) $this->debit_note_prefix) ?: 'ND';
        $consecutive = $this->debit_note_consecutive;
        return "{$prefix}-{$consecutive}";
    }
}
