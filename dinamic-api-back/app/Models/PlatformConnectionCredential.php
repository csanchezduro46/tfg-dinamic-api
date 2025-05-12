<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlatformConnectionCredential extends Model
{
    protected $fillable = ['platform_connection_id', 'necessary_key_id', 'value'];

    protected $hidden = ['value'];

    public function connection()
    {
        return $this->belongsTo(PlatformConnection::class, 'platform_connection_id');
    }

    public function necessaryKey()
    {
        return $this->belongsTo(PlatformNecessaryKey::class, 'necessary_key_id');
    }
}
