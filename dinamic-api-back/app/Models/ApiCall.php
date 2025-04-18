<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApiCall extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'platform_version_id',
        'name',
        'endpoint',
        'method',
        'request_type',
        'response_type',
        'payload_example',
        'response_example',
        'description'
    ];

    protected $casts = [
        'payload_example' => 'array',
        'response_example' => 'array',
    ];

    public function platformVersion(): BelongsTo
    {
        return $this->belongsTo(PlatformVersion::class);
    }
}
