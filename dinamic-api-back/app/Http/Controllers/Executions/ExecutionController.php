<?php

namespace App\Http\Controllers\Executions;

use App\Http\Controllers\Controller;
use App\Models\ApiCallMapping;
use App\Models\Execution;
use App\Services\Executions\ExecutionService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\RequestBody;

class ExecutionController extends Controller
{
    private ExecutionService $executionService;

    public function __construct(ExecutionService $executionService)
    {
        $this->executionService = $executionService;
    }

    /**
     * @Endpoint(description: "Devuelve la lista de ejecuciones (manuales o programadas) visibles para el usuario actual")
     */
    public function list()
    {
        $type = request('type'); // puede ser 'manual', 'scheduled' o null

        $query = Execution::with('mapping');

        if ($type) {
            $query->where('execution_type', $type);
        }

        if (!Auth::user()->hasRole('admin')) {
            $userMappingIds = Auth::user()->apiCallMappings()->pluck('id');
            $query->whereIn('api_call_mapping_id', $userMappingIds);
        }

        return response()->json($query->orderByDesc('created_at')->get());
    }

    /**
     * @Endpoint(description: "Devuelve todas las ejecuciones asociadas a un mapeo concreto")
     */
    public function listByMapping($id)
    {
        $mapping = ApiCallMapping::findOrFail($id);
        if ($mapping->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403);
        }

        $executions = Execution::where('api_call_mapping_id', '=', $id)->get();
        return response()->json($executions);
    }

    /**
     * @Endpoint(description: "Crea una nueva ejecución (manual o programada)")
     * @RequestBody(
     *     content: "application/json",
     *     example: {
     *         "execution_type": "manual",
     *         "started_at": "2025-05-21T10:00:00Z",
     *         "repeat": "none",
     *         "cron_expression": null
     *     }
     * )
     */
    public function store($mappingId, Request $request)
    {
        $mapping = ApiCallMapping::findOrFail($mappingId);
        if ($mapping->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403);
        }

        $validator = Validator::make($request->all(), [
            'execution_type' => 'required|in:manual,scheduled',
            'started_at' => 'nullable|date',
            'repeat' => 'nullable|in:none,hourly,daily,weekly,custom',
            'cron_expression' => 'nullable|string|required_if:repeat,custom',
        ], [
            'execution_type.required' => 'El tipo de ejecución es obligatoria.',
            'execution_type.in' => 'Debes indicar el tipo de ejecución que deseas: manual o programada (scheduled).',
            'started_at.date' => 'La fecha de inicio no tiene un formato válido.',
            'repeat.in' => 'El campo repeat debe ser: none, hourly, daily, weekly o custom.',
            'cron_expression.required_if' => 'La expresión CRON es obligatoria si seleccionas "custom".'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'msg' => 'Error en la petición',
                'errors' => $validator->errors()
            ], 400);
        }

        $validated = $validator->validated();
        $type = $validated['execution_type'];
        $repeat = $validated['repeat'] ?? 'none';
        $cron = $validated['cron_expression'] ?? null;
        $status = 'pending';
        $startTime = $validated['started_at'] ?? now();

        $execution = Execution::create([
            'api_call_mapping_id' => $mappingId,
            'execution_type' => $type,
            'status' => $status,
            'started_at' => $startTime,
            'repeat' => $repeat,
            'cron_expression' => $cron,
            'last_executed_at' => null,
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
                $msg = 'Ejecución programada creada correctamente.';
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

    /**
     * @Endpoint(description: "Ejecuta manualmente una ejecución previamente registrada")
     */
    public function execute($id)
    {
        $execution = Execution::findOrFail($id);
        $mapping = $execution->mapping;
        if ($mapping->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403);
        }

        $msg = '';
        $log = '';
        try {
            $response = $this->executionService->runExecution($execution);
            $msg = $response['status'] == 'success' ? 'Ejecución completada correctamente.' : 'Error durante la ejecución.';
            $code = $response['status'] == 'success' ? 200 : 500;
            $execution->update([
                'status' => $response['status'] ?? $execution->status,
                'finished_at' => now(),
                'last_executed_at' => now()
            ]);
            $log = $response;
            $execution->refresh();
        } catch (\Throwable $e) {
            $execution->update([
                'status' => 'failed',
                'response_log' => ['error' => $e->getMessage()],
                'finished_at' => now()
            ]);
            $msg = 'Error durante la ejecución.';
            $log = $msg;
            $code = 500;
        }

        return response()->json([
            'msg' => $msg,
            'execution' => $execution,
            'log' => $log
        ], $code);
    }

    /**
     * @Endpoint(description: "Actualiza una ejecución programada (fecha de inicio, repetición, cron)")
     * @RequestBody(
     *     content: "application/json",
     *     example: {
     *         "started_at": "2025-05-22T08:00:00Z",
     *         "repeat": "custom",
     *         "cron_expression": "0 2 * * *"
     *     }
     * )
     */
    public function update(Request $request, $id)
    {
        $execution = Execution::findOrFail($id);

        if ($execution->mapping->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403);
        }

        $validator = Validator::make($request->all(), [
            'started_at' => 'nullable|date',
            'repeat' => 'nullable|in:none,hourly,daily,weekly,custom',
            'cron_expression' => 'nullable|string|required_if:repeat,custom',
        ], [
            'started_at.date' => 'La fecha de inicio no tiene un formato válido.',
            'repeat.in' => 'El campo repeat debe ser: none, hourly, daily, weekly o custom.',
            'cron_expression.required_if' => 'La expresión CRON es obligatoria si seleccionas "custom".'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'msg' => 'Error en la validación',
                'errors' => $validator->errors()
            ], 400);
        }

        $execution->update($validator->validated());

        return response()->json([
            'msg' => 'Ejecución actualizada correctamente.',
            'execution' => $execution
        ]);
    }

    /**
     * @Endpoint(description: "Elimina una ejecución (manual o programada)")
     */
    public function delete($id)
    {
        $execution = Execution::findOrFail($id);

        if ($execution->mapping->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403);
        }

        $execution->delete();

        return response()->json(['msg' => 'Ejecución eliminada correctamente.']);
    }

    /**
     * @Endpoint(description: "Lanza el comando Laravel para ejecutar sincronizaciones programadas (solo admin)")
     */
    public function runScheduledCommand()
    {
        if (!auth()->user()?->hasRole('admin')) {
            return response()->json(['msg' => 'No autorizado'], 403);
        }

        Artisan::call('app:scheduled-executions');

        return response()->json([
            'msg' => 'Comando lanzado correctamente',
            'output' => Artisan::output()
        ]);
    }

}
