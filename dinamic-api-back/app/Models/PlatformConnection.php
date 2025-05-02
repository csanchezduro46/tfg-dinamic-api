<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlatformConnection extends Model
{
    protected $fillable = [
        'user_id',
        'platform_version_id',
        'name',
        'store_url',
        'status',
        'last_checked_at',
        'config'
    ];

    protected $casts = [
        'config' => 'array',
        'last_checked_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function credentials()
    {
        return $this->hasMany(PlatformConnectionCredential::class);
    }

    public function version()
    {
        return $this->belongsTo(\App\Models\PlatformVersion::class, 'platform_version_id');
    }
}
