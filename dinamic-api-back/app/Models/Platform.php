<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Platform extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug'
    ];

    public function versions(): HasMany
    {
        return $this->hasMany(PlatformVersion::class);
    }

    public function necessaryKeys()
    {
        return $this->hasMany(\App\Models\PlatformNecessaryKey::class);
    }

    public function connections()
    {
        return $this->hasMany(PlatformConnection::class);
    }
}
