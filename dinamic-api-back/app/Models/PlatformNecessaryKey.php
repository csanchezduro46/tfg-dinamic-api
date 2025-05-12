<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlatformNecessaryKey extends Model
{
    protected $fillable = ['platform_id', 'key', 'label', 'required'];

    public function platform(): BelongsTo
    {
        return $this->belongsTo(Platform::class);
    }

    public function necessaryKey()
    {
        return $this->hasMany(PlatformConnectionCredential::class);
    }
}
