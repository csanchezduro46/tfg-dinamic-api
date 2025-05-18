<?php

namespace App\Http\Controllers\Executions;

use App\Http\Controllers\Controller;
use App\Models\Execution;
use App\Models\HistoryExecution;
use Illuminate\Support\Facades\Auth;
use Dedoc\Scramble\Attributes\Endpoint;

class HistoryExecutionController extends Controller
{
    /**
     * @Endpoint(description: "Devuelve el historico de las ejecuciones de todas las sincronizaciones")
     */
    public function show($executionId)
    {
        $execution = Execution::findOrFail($executionId);
        if ($execution->mapping->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403);
        }

        return response()->json($execution->history()->get());
    }
}
