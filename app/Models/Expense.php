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
        if ($this->invoice_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->invoice_path)) {
            return asset('storage/' . $this->invoice_path);
        }
        return null;
    }

    public function isPdf(): bool
    {
        return $this->invoice_path && strtolower(pathinfo($this->invoice_path, PATHINFO_EXTENSION)) === 'pdf';
    }
}
