<?php

namespace App\Models\Address;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tambons extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name_th',
        'name_en',
        'amphure_id',
        'zip_code',
    ];

    public function amphure(): BelongsTo
    {
        return $this->belongsTo(Amphures::class, 'amphure_id');
    }
}
