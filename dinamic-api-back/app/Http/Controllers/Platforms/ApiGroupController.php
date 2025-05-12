<?php

namespace App\Http\Controllers\Platforms;

use App\Http\Controllers\Controller;
use App\Models\ApiCallGroup;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class ApiGroupController extends Controller
{
    // Obtener todos los grupos (cualquier usuario logueado)
    public function getAll(): JsonResponse
    {
        return response()->json(ApiCallGroup::all());
    }

    // Crear un grupo (admin)
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

    // Actualizar un grupo (admin)
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

    // Eliminar un grupo (admin)
    public function delete($id): JsonResponse
    {
        $group = ApiCallGroup::findOrFail($id);
        $group->delete();

        return response()->json([
            'msg' => 'Grupo eliminado correctamente.'
        ]);
    }
}
