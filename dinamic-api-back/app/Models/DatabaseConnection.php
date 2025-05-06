<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class DatabaseConnection extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'driver',
        'host',
        'port',
        'database',
        'username',
        'password',
        'status'
    ];

    protected $hidden = ['username', 'password'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getDecryptedCredentials(): array
    {
        return [
            'driver' => $this->driver,
            'host' => $this->host,
            'port' => $this->port,
            'database' => $this->database,
            'username' => Crypt::decryptString($this->username),
            'password' => Crypt::decryptString($this->password),
        ];
    }
}
