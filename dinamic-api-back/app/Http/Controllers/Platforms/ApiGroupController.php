<?php

namespace App\Http\Controllers\Platforms;

use App\Http\Controllers\Controller;
use App\Models\ApiCallGroup;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\RequestBody;

class ApiGroupController extends Controller
{
    /**
     * @Endpoint(description: "Obtiene todos los grupos de llamadas API disponibles.")
     */
    public function getAll(): JsonResponse
    {
        return response()->json(ApiCallGroup::all());
    }

    /**
     * @Endpoint(description: "Crea un nuevo grupo de llamadas API (solo administradores).")
     * @RequestBody(
     *     content: "application/json",
     *     example: {
     *         "name": "Clientes"
     *     }
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:api_call_group,name|max:100',
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'name.unique' => 'El nombre ya existe.',
            'name.max' => 'El nombre no puede tener más de 100 caracteres.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'msg' => 'Error en la petición',
                'errors' => $validator->errors()
            ], 400);
        }

        $group = ApiCallGroup::create(['name' => $request->name]);

        return response()->json([
            'msg' => 'Grupo creado correctamente.',
            'group' => $group
        ], 201);
    }

    /**
     * @Endpoint(description: "Actualiza el nombre de un grupo de llamadas API (solo administradores).")
     * @RequestBody(
     *     content: "application/json",
     *     example: {
     *         "name": "Clientes Premium"
     *     }
     * )
     */
    public function update(Request $request, $id): JsonResponse
    {
        $group = ApiCallGroup::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:api_call_group,name|max:100',
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'name.unique' => 'El nombre ya existe.',
            'name.max' => 'El nombre no puede tener más de 100 caracteres.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'msg' => 'Error en la petición',
                'errors' => $validator->errors()
            ], 400);
        }

        $group->update(['name' => $request->name]);

        return response()->json([
            'msg' => 'Grupo actualizado correctamente.',
            'group' => $group
        ]);
    }

    /**
     * @Endpoint(description: "Elimina un grupo de llamadas API existente (solo administradores).")
     */
    public function delete($id): JsonResponse
    {
        $group = ApiCallGroup::findOrFail($id);
        $group->delete();

        return response()->json([
            'msg' => 'Grupo eliminado correctamente.'
        ]);
    }
}
