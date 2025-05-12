<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoryExecution extends Model
{
    protected $fillable = [
        'execution_id',
        'status',
        'log',
        'launched_at'
    ];

    protected $casts = [
        'launched_at' => 'datetime',
    ];

    public function execution()
    {
        return $this->belongsTo(Execution::class, 'execution_id');
    }
}
