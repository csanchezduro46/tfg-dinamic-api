<?php

namespace App\Http\Controllers\Executions;

use App\Http\Controllers\Controller;
use App\Models\ApiCallMapping;
use App\Models\Execution;
use App\Services\Executions\ExecutionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ExecutionController extends Controller
{
    private ExecutionService $executionService;

    public function __construct(ExecutionService $executionService)
    {
        $this->executionService = $executionService;
    }

    public function list()
    {
        if (!Auth::user()->hasRole('admin')) {
            return response()->json(Execution::with(['mapping'])->get());
        }
        return response()->json(Execution::with(['mapping'])->where('apiCallMapping.user', '=', Auth::user()->getId())->get());
    }

    public function listByMapping($id)
    {
        $mapping = ApiCallMapping::findOrFail($id);
        if ($mapping->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403);
        }

        $executions = Execution::where('api_call_mapping_id', '=', $id)->get();
        return response()->json($executions);
    }

    public function execute($mappingId, Request $request)
    {
        $mapping = ApiCallMapping::findOrFail($mappingId);
        if ($mapping->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403);
        }

        $validator = Validator::make($request->all(), [
            'execution_type' => 'required|in:manual,scheduled',
            'started_at' => 'nullable|date',
        ], [
            'execution_type.required' => 'El tipo de ejecución es obligatoria.',
            'execution_type.in' => 'Debes indicar el tipo de ejecución que deseas: manual o programada (scheduled).',
            'started_at.date' => 'La fecha de inicio no tiene un formato válido.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'msg' => 'Error en la petición',
                'errors' => $validator->errors()
            ], 400);
        }

        $validated = $validator->validated();
        $type = $validated['execution_type'];
        $status = 'pending';
        $startTime = $validated['started_at'] ?? now();

        $execution = Execution::create([
            'api_call_mapping_id' => $mappingId,
            'execution_type' => $type,
            'status' => $status,
            'started_at' => $startTime
        ]);

        $msg = $type == 'manual' ? 'Ejecución creada' : 'Ejecución creada (aún no procesada)';
        $code = 201;
        switch ($type) {
            case 'manual':
                try {
                    $response = $this->executionService->runExecution($execution);
                    $msg = $response['status'] == 'success' ? 'Ejecución manual completada.' : 'Error en la ejecución.';
                    $code = $response['status'] == 'success' ? 201 : 400;
                    $execution->refresh();
                } catch (\Throwable $e) {
                    $execution->update([
                        'status' => 'failed',
                        'response_log' => ['error' => $e->getMessage()],
                        'finished_at' => now()
                    ]);
                    $msg = 'Error durante la ejecución manual.';
                    $code = 500;
                }
                break;
            case 'scheduled':
                $msg = 'Ejecución programada registrada.';
                break;
            default:
                $msg = 'No se ha podido crear la petición';
                $code = 400;
                $execution->delete();
                break;
        }

        return response()->json([
            'msg' => $msg,
            'execution' => $execution
        ], $code);
    }
}
