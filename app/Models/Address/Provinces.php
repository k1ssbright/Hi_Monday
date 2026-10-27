<?php

namespace App\Models\Address;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Provinces extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name_th',
        'name_en',
        'geography_id',
    ];

    public function geography(): BelongsTo
    {
        return $this->belongsTo(Geographies::class, 'geography_id');
    }

    public function amphures(): HasMany
    {
        return $this->hasMany(Amphures::class, 'province_id');
    }
}
