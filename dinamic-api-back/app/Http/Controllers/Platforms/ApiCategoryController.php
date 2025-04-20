<?php

namespace App\Http\Controllers\Platforms;

use App\Http\Controllers\Controller;
use App\Models\ApiCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class ApiCategoryController extends Controller
{
    // Obtener todas las categorías (cualquier usuario logueado)
    public function getAll(): JsonResponse
    {
        return response()->json(ApiCategory::all());
    }

    // Crear una categoría (admin)
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:api_categories,name|max:100',
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

        $category = ApiCategory::create(['name' => $request->name]);

        return response()->json([
            'msg' => 'Categoría creada correctamente.',
            'category' => $category
        ], 201);
    }

    // Actualizar una categoría (admin)
    public function update(Request $request, $id): JsonResponse
    {
        $category = ApiCategory::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:api_categories,name|max:100',
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

        $category->update(['name' => $request->name]);

        return response()->json([
            'msg' => 'Categoría actualizada correctamente.',
            'category' => $category
        ]);
    }

    // Eliminar una categoría (admin)
    public function delete($id): JsonResponse
    {
        $category = ApiCategory::findOrFail($id);
        $category->delete();

        return response()->json([
            'msg' => 'Categoría eliminada correctamente.'
        ]);
    }
}
