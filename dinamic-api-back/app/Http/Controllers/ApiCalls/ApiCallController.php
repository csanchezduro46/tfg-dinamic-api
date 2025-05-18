<?php

namespace App\Http\Controllers\ApiCalls;

use App\Http\Controllers\Controller;
use App\Models\ApiCall;
use App\Models\PlatformVersion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\RequestBody;

class ApiCallController extends Controller
{
    /**
     * @Endpoint(description: 'Devuelve todas las llamadas API registradas con su grupo y versión')
     */
    public function getAll()
    {
        return response()->json(ApiCall::with('group', 'version.platform')->get());
    }

    /**
     * @Endpoint(description: 'Devuelve una llamada API concreta por ID')
     */
    public function get($id)
    {
        return response()->json(ApiCall::with('group', 'version.platform')->findOrFail($id));
    }

    /**
     * @Endpoint(description: 'Devuelve todas las llamadas API de una versión de plataforma específica')
     */
    public function getByPlatformVersion($versionId)
    {
        $platformVersion = PlatformVersion::findOrFail($versionId);

        return response()->json(ApiCall::where('platform_version_id', $platformVersion->id)->with('group')->get());
    }

    /**
     * @Endpoint(description: 'Crea una nueva llamada API asociada a una plataforma')
     * @RequestBody(
     *     content: {
     *         "application/json": {
     *             "example": {
     *                 "platform_version_id": 1,
     *                 "name": "createCustomer",
     *                 "group_id": 2,
     *                 "endpoint": "/admin/api/graphql.json",
     *                 "method": "POST",
     *                 "request_type": "graphql",
     *                 "response_type": "json",
     *                 "payload_example": { "query": "...", "variables": { "input": {...} } },
     *                 "response_example": { "data": {...} },
     *                 "description": "Crea un nuevo cliente en Shopify"
     *             }
     *         }
     *     }
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'platform_version_id' => 'required|exists:platform_versions,id',
            'name' => 'required|string|max:100',
            'group_id' => 'required|exists:api_call_groups,id',
            'endpoint' => 'required|string|max:255',
            'method' => 'required|string|max:10',
            'request_type' => 'nullable|string|max:20',
            'response_type' => 'nullable|string|max:20',
            'payload_example' => 'nullable|array',
            'response_example' => 'nullable|array',
            'description' => 'nullable|string|max:255'
        ], [
            'platform_version_id.required' => 'La versión de la plataforma es obligatoria.',
            'platform_version_id.exists' => 'La version de la plataforma debe de existir en la aplicación.',
            'name.required' => 'El nombre de la llamada es obligatorio.',
            'group_id.required' => 'El id del grupo es obligatorio, la llamada debe pertenecer a un grupo concreto.',
            'group_id.exists' => 'El grupo no es válido, debe de existir en la aplicación.',
            'endpoint.required' => 'La ruta de la llamada es obligatoria.',
            'method.required' => 'El método que utiliza la llamada es obligatorio.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'msg' => 'Error en la petición',
                'errors' => $validator->errors()
            ], 400);
        }

        $validated = $validator->validated();
        $apiCall = ApiCall::create($validated);

        return response()->json([
            'msg' => 'Llamada creada correctamente.',
            'api_call' => $apiCall
        ], 201);
    }

    /**
     * @Endpoint(description: 'Actualiza los datos de una llamada API existente')
     */
    public function update(Request $request, $id)
    {
        $apiCall = ApiCall::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:100',
            'group_id' => 'sometimes|exists:api_call_groups,id',
            'endpoint' => 'sometimes|string|max:255',
            'method' => 'sometimes|string|max:10',
            'request_type' => 'nullable|string|max:20',
            'response_type' => 'nullable|string|max:20',
            'payload_example' => 'nullable|array',
            'response_example' => 'nullable|array',
            'description' => 'nullable|string|max:255'
        ], [
            'group_id.exists' => 'El grupo no es válido, debe de existir en la aplicación.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'msg' => 'Error en la petición',
                'errors' => $validator->errors()
            ], 400);
        }

        $validated = $validator->validated();
        $apiCall->update($validated);

        return response()->json([
            'msg' => 'Llamada actualizada correctamente.',
            'api_call' => $apiCall
        ]);
    }

    /**
     * @Endpoint(description: 'Elimina una llamada API por ID')
     */
    public function delete($id)
    {
        $apiCall = ApiCall::findOrFail($id);
        $apiCall->delete();

        return response()->json([
            'msg' => 'Llamada eliminada correctamente.'
        ]);
    }

    /**
     * @Endpoint(description: 'Devuelve los campos disponibles de una llamada API según su payload')
     */
    public function getFields($id)
    {
        $call = ApiCall::findOrFail($id);
        $fields = $this->extractFieldsWithLabels($call->payload_example);

        return response()->json(['fields' => $fields]);
    }

    private function extractFieldsWithLabels(array $data, string $prefix = ''): array
    {
        $fields = [];

        foreach ($data as $key => $value) {
            $isAssoc = is_array($value) && array_keys($value) !== range(0, count($value) - 1);
            $cleanKey = ucfirst(str_replace('_', ' ', $key));
            $path = $prefix ? "$prefix.$key" : $key;
            $label = $prefix ? $this->labelFromPath($path) : $cleanKey;

            if (is_array($value) && !$isAssoc) {
                $first = $value[0] ?? [];
                if (is_array($first)) {
                    $fields = array_merge($fields, $this->extractFieldsWithLabels($first, "$path"));
                }
            } elseif (is_array($value)) {
                $fields = array_merge($fields, $this->extractFieldsWithLabels($value, $path));
            } else {
                $fields[] = [
                    'path' => $path,
                    'label' => $label
                ];
            }
        }

        return $fields;
    }

    private function labelFromPath(string $path): string
    {
        $segments = explode('.', str_replace(['[0]', '_'], [' →', ' '], $path));
        $readable = array_map(fn($s) => Str::title(str_replace('_', ' ', $s)), $segments);
        return implode(' → ', $readable);
    }
}