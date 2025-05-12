<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApiCallMappingField extends Model
{
    protected $fillable = [
        'api_call_mapping_id',
        'source_field',
        'target_field'
    ];

    public function mapping(): BelongsTo
    {
        return $this->belongsTo(ApiCallMapping::class, 'api_call_mapping_id');
    }
}