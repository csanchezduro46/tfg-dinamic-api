<?php

namespace App\Http\Controllers\ApiCalls;

use App\Http\Controllers\Controller;
use App\Models\ApiCallMapping;
use App\Models\ApiCallMappingField;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\RequestBody;

class ApiCallMappingController extends Controller
{
    /**
     * @Endpoint(description: 'Devuelve todos los mapeos disponibles para el usuario actual o todos si es admin')
     */
    public function getMappings()
    {
        if (Auth::user()->hasRole('admin')) {
            return response()->json(ApiCallMapping::with(['user', 'sourceApiCall', 'targetApiCall', 'sourceDb', 'targetDb', 'fields'])->get());
        }
        return response()->json(Auth::user()->apiCallMappings()->with(['sourceApiCall', 'targetApiCall', 'sourceDb', 'targetDb', 'fields'])->get());
    }

    /**
     * @Endpoint(description: 'Devuelve un mapeo por ID incluyendo campos y relaciones')
     */
    public function getSingle($id)
    {
        $mapping = ApiCallMapping::with('fields')->findOrFail($id);

        if ($mapping->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            return response()->json(['msg' => 'Forbidden'], 403);
        }

        return response()->json($mapping->load('fields', 'sourceApiCall', 'targetApiCall'));
    }

    /**
     * @Endpoint(description: 'Crea un nuevo mapeo de conexión entre origen y destino')
     * @RequestBody(
     *     content="application/json",
     *     example={
     *         "name": "Mapeo clientes",
     *         "direction": "to_api",
     *         "source_db_connection_id": 1,
     *         "source_table": "clientes",
     *         "target_api_call_id": 1,
     *         "fields": {
     *             {"source_field": "email", "target_field": "customer.email"}
     *         }
     *     }
     * )
     */
    public function store(Request $request)
    {
        if (Auth::user()->hasRole('admin')) {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|integer|exists:users,id',
                'name' => 'required|string|max:100',
                'direction' => 'required|in:to_api,from_api,both',
                'description' => 'nullable|string',
                'source_api_call_id' => 'nullable|exists:api_calls,id',
                'source_db_connection_id' => 'nullable|exists:database_connections,id',
                'source_table' => 'nullable|string|max:100',
                'target_api_call_id' => 'nullable|exists:api_calls,id',
                'target_db_connection_id' => 'nullable|exists:database_connections,id',
                'target_table' => 'nullable|string|max:100',
                'fields' => 'nullable|array'
            ], [
                'name.required' => 'El nombre del mapeo es obligatorio.',
                'direction.required' => 'La dirección del mapeo es obligatoria.',
                'user_id.required' => 'El usuario es obligatorio para un mapeo creado por un administrador.',
                'user_id.exists' => 'El usuario debe existir'
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:100',
                'direction' => 'required|in:to_api,from_api,both',
                'description' => 'nullable|string',
                'source_api_call_id' => 'nullable|exists:api_calls,id',
                'source_db_connection_id' => 'nullable|exists:database_connections,id',
                'source_table' => 'nullable|string|max:100',
                'target_api_call_id' => 'nullable|exists:api_calls,id',
                'target_db_connection_id' => 'nullable|exists:database_connections,id',
                'target_table' => 'nullable|string|max:100',
                'fields' => 'nullable|array'
            ], [
                'name.required' => 'El nombre del mapeo es obligatorio.',
                'direction.required' => 'La dirección del mapeo es obligatoria.',
                'user_id.required' => 'El usuario es obligatorio para un mapeo creado por un administrador.',
                'user_id.exists' => 'El usuario debe existir'
            ]);
        }

        if ($validator->fails()) {
            return response()->json([
                'msg' => 'Error en la petición',
                'errors' => $validator->errors()
            ], 400);
        }

        $validated = $validator->validated();
        $errors = [];

        // Mínimo hay que indicar un tipo de conexión
        $hasSource = $validated['source_api_call_id'] ?? $validated['source_db_connection_id'] ?? null;
        $hasTarget = $validated['target_api_call_id'] ?? $validated['target_db_connection_id'] ?? null;

        if (!$hasSource) {
            $errors['origen'] = 'Debes indicar un origen (API o conexión de BBDD)';
        }

        if (!$hasTarget) {
            $errors['destino'] = 'Debes indicar un destino (API o conexión de BBDD)';
        }

        if (isset($validated['source_db_connection_id']) && !isset($validated['source_table'])) {
            $errors['source_table'] = 'Debes indicar una tabla de origen para la conexión de BBDD';
        }

        if (isset($validated['target_db_connection_id']) && !isset($validated['target_table'])) {
            $errors['target_table'] = 'Debes indicar una tabla de destino para la conexión de BBDD';
        }

        foreach ($request->fields as $field) {
            if (!isset($field['source_field']) || !isset($field['target_field'])) {
                $errors['fields'] = 'Debes indicar los campos de origen y destino (source_field y target_field) de los campos que quieres relacionar';
            }
        }

        if (!empty($errors)) {
            return response()->json([
                'msg' => 'Error en la petición',
                'errors' => $errors
            ], 400);
        }

        if (!Auth::user()->hasRole('admin')) {
            $validated['user_id'] = Auth::id();
        }

        $mapping = ApiCallMapping::create($validated);

        foreach ($request->fields as $field) {
            ApiCallMappingField::create([
                'api_call_mapping_id' => $mapping->id,
                'source_field' => $field['source_field'],
                'target_field' => $field['target_field']
            ]);
        }

        return response()->json([
            'msg' => 'Mapeo creado correctamente.',
            'mapping' => $mapping->load('fields')
        ], 201);
    }

    /**
     * @Endpoint(description: 'Actualiza un mapeo existente y sus campos')
     */
    public function update(Request $request, $id)
    {
        $mapping = ApiCallMapping::with('fields')->findOrFail($id);

        if ($mapping->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            return response()->json(['msg' => 'Forbidden'], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:100',
            'direction' => 'sometimes|in:to_api,from_api,both',
            'description' => 'nullable|string',
            'source_api_call_id' => 'nullable|exists:api_calls,id',
            'source_db_connection_id' => 'nullable|exists:database_connections,id',
            'source_table' => 'nullable|string|max:100',
            'target_api_call_id' => 'nullable|exists:api_calls,id',
            'target_db_connection_id' => 'nullable|exists:database_connections,id',
            'target_table' => 'nullable|string|max:100',
            'fields' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'msg' => 'Error en la petición',
                'errors' => $validator->errors()
            ], 400);
        }

        $validated = $validator->validated();
        $mapping->update($validated);

        if ($request->has('fields')) {
            $mapping->fields()->delete();
            foreach ($request->fields as $field) {
                ApiCallMappingField::create([
                    'api_call_mapping_id' => $mapping->id,
                    'source_field' => $field['source_field'],
                    'target_field' => $field['target_field']
                ]);
            }
        }

        return response()->json([
            'msg' => 'Mapeo actualizado correctamente.',
            'mapping' => $mapping->load('fields')
        ]);
    }

    /**
     * @Endpoint(description: 'Elimina un mapeo de conexiones y sus relaciones')
     */
    public function delete($id)
    {
        $mapping = ApiCallMapping::findOrFail($id);

        if ($mapping->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            return response()->json(['msg' => 'Forbidden'], 403);
        }

        $mapping->delete();
        return response()->json(['msg' => 'Mapeo eliminado correctamente.']);
    }

    /**
     * @Endpoint(description: 'Permite probar la ejecución de un mapeo concreto')
     */
    public function testMapping($id)
    {
        $mapping = ApiCallMapping::findOrFail($id);

        if ($mapping->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            return response()->json(['msg' => 'Forbidden'], 403);
        }

        $mapping->delete();
        return response()->json(['msg' => 'Mapeo eliminado correctamente.']);
    }
}
