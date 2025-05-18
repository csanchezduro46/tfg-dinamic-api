<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Execution extends Model
{
    protected $fillable = [
        'api_call_mapping_id',
        'status',
        'execution_type',
        'response_log',
        'started_at',
        'finished_at',
        'repeat',
        'cron_expression',
        'last_executed_at'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'last_executed_at' => 'datetime',
    ];

    public function mapping()
    {
        return $this->belongsTo(ApiCallMapping::class, 'api_call_mapping_id');
    }

    public function history()
    {
        return $this->hasMany(HistoryExecution::class, 'execution_id');
    }
}
