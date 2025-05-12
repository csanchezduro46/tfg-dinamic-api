<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ApiCallMapping extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'direction',
        'description',
        'source_api_call_id',
        'source_db_connection_id',
        'source_table',
        'target_api_call_id',
        'target_db_connection_id',
        'target_table'
    ];
    

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sourceApiCall(): BelongsTo
    {
        return $this->belongsTo(ApiCall::class, 'source_api_call_id');
    }

    public function targetApiCall(): BelongsTo
    {
        return $this->belongsTo(ApiCall::class, 'target_api_call_id');
    }

    public function sourceDb(): BelongsTo
    {
        return $this->belongsTo(DatabaseConnection::class, 'source_db_connection_id');
    }

    public function targetDb(): BelongsTo
    {
        return $this->belongsTo(DatabaseConnection::class, 'target_db_connection_id');
    }

    public function fields(): HasMany
    {
        return $this->hasMany(ApiCallMappingField::class);
    }

    public function executions(): HasMany
    {
        return $this->hasMany(Execution::class);
    }
}