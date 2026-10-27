<?php

namespace App\Models\Address;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Amphures extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name_th',
        'name_en',
        'province_id',
    ];

    public function province(): BelongsTo
    {
        return $this->belongsTo(Provinces::class, 'province_id');
    }

    public function tambons(): HasMany
    {
        return $this->hasMany(Tambons::class, 'amphure_id');
    }
}
