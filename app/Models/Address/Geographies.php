<?php

namespace App\Models\Address;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Geographies extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name',
    ];

    public function provinces(): HasMany
    {
        return $this->hasMany(Provinces::class, 'geography_id');
    }
}
