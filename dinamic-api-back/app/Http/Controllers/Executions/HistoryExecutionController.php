<?php

namespace App\Http\Controllers\Executions;

use App\Http\Controllers\Controller;
use App\Models\Execution;
use App\Models\HistoryExecution;
use Illuminate\Support\Facades\Auth;

class HistoryExecutionController extends Controller
{
    public function show($executionId)
    {
        $execution = Execution::findOrFail($executionId);
        if ($execution->mapping->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403);
        }

        return response()->json($execution->history()->get());
    }
}
