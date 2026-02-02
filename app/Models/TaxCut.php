<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use App\Services\TaxCutCalculator;

class TaxCut extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'recipient_type',
        'recipient_id',
        'memo_number',
        'invoice_number',
        'invoice_date',
        'commission_amount',
        'dpp_amount',
        'tax_rate',
        'tax_amount',
        'net_payment',
        'pph_method',
        'cut_date',
        'deposited_date',
        'approved_by',
        'notes',
    ];

    protected $casts = [
        'commission_amount' => 'decimal:2',
        'dpp_amount'        => 'decimal:2',
        'tax_rate'          => 'decimal:2',
        'tax_amount'        => 'decimal:2',
        'net_payment'       => 'decimal:2',
        'invoice_date'      => 'date',
        'cut_date'          => 'date',
        'deposited_date'    => 'date',
    ];

    /* ============================
     * LOCKED TAX CALCULATION
     * ============================ */

    protected static function booted(): void
    {
        static::saving(function (TaxCut $model) {
            // default method jika null
            $method = $model->pph_method ?: 'gross';
            $model->pph_method = $method;

            // pastikan commission_amount tidak null
            $commission = (float) $model->commission_amount;

            // hitung dpp, tax, net via service
            $result = TaxCutCalculator::calculate(
                $commission,
                (float) $model->tax_rate,
                $method
            );

            $model->dpp_amount  = $result['dpp_amount'];
            $model->tax_amount  = $result['tax_amount'];
            $model->net_payment = $result['net_payment'];
        });
    }

    /* ============================
     * RELATIONS
     * ============================ */

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function recipient(): MorphTo
    {
        return $this->morphTo();
    }

    /* ============================
     * HELPER / ACCESSORS
     * ============================ */

    /**
     * Return recipient name, fallback "-"
     */
    public function getRecipientNameAttribute(): string
    {
        return $this->recipient?->name ?? '-';
    }

    /**
     * Return recipient NPWP
     */
    public function getRecipientNpwpAttribute(): ?string
    {
        return $this->recipient?->npwp;
    }

    /**
     * Hitung pajak via service, untuk print/export atau preview
     */
    public function calculate(): array
    {
        return TaxCutCalculator::calculate(
            (float) $this->commission_amount,
            (float) $this->tax_rate,
            $this->pph_method ?: 'gross'
        );
    }
}
