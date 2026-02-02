<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'npwp',
        'address',
        'phone',
        'email',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function taxCuts(): MorphMany
    {
        return $this->morphMany(TaxCut::class, 'recipient');
    }

    public function getFormattedNpwpAttribute(): ?string
    {
        if (!$this->npwp) return null;
        
        $npwp = $this->npwp;
        if (strlen($npwp) === 15) {
            return substr($npwp, 0, 2) . '.' . 
                   substr($npwp, 2, 3) . '.' . 
                   substr($npwp, 5, 3) . '.' . 
                   substr($npwp, 8, 1) . '-' . 
                   substr($npwp, 9, 3) . '.' . 
                   substr($npwp, 12, 3);
        }
        return $npwp;
    }
}
