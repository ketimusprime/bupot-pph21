<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'npwp',
        'address',
        'phone',
        'email',
        'director_name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function taxCuts(): HasMany
    {
        return $this->hasMany(TaxCut::class);
    }

    public function getFormattedNpwpAttribute(): string
    {
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
