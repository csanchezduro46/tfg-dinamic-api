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
        'group_id',
        'name',
        'group',
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

    public function version()
    {
        return $this->belongsTo(PlatformVersion::class, 'platform_version_id');
    }

    public function group()
    {
        return $this->belongsTo(ApiCallGroup::class, 'group_id');
    }
}
