<?php namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'company_id', 'concept', 'category', 'amount', 'date', 'description', 'invoice_path', 'invoice_filename'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'date',
    ];

    public function company() { return $this->belongsTo(Company::class); }

    public function getInvoiceUrlAttribute()
    {
        if ($this->invoice_path) {
            return route('business.expenses.invoice', $this->id);
        }
        return null;
    }

    public function isPdf(): bool
    {
        return $this->invoice_path && strtolower(pathinfo($this->invoice_path, PATHINFO_EXTENSION)) === 'pdf';
    }

    public function getFormattedAmountAttribute(): string
    {
        $val = (float) $this->amount;
        $hasDecimals = floor($val) != $val;
        $currency = $this->company?->effective_currency ?? 'COP';
        if ($currency === 'MXN') {
            return '$ ' . number_format($val, 2, '.', ',') . ' MXN';
        }
        return '$ ' . number_format($val, $hasDecimals ? 2 : 0, ',', '.') . ' COP';
    }
}
