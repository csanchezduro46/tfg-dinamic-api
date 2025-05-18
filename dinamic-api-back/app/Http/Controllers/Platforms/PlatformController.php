<?php

namespace App\Http\Controllers\Platforms;

use App\Http\Controllers\Controller;
use App\Models\Platform;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\RequestBody;

class PlatformController extends Controller
{
    /**
     * @Endpoint(description: "Devuelve la lista de plataformas disponibles. Se puede buscar por nombre con el parámetro `name`.")
     */
    public function getPlatformsSearch(Request $request): JsonResponse
    {
        $query = Platform::query();

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        return response()->json($query->get());
    }

    /**
     * @Endpoint(description: "Crea una nueva plataforma (solo administradores).")
     * @RequestBody(
     *     content: "application/json",
     *     example: {
     *         "name": "Shopify",
     *         "slug": "shopify"
     *     }
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'slug' => 'required|string|max:100|unique:platforms,slug'
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'slug.required' => 'El slug es obligatorio.',
            'slug.unique' => 'El slug ya existe.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'msg' => 'Error en la petición',
                'errors' => $validator->errors()
            ], 400);
        }

        $platform = Platform::create([
            'name' => $request->name,
            'slug' => $request->slug,
        ]);

        return response()->json([
            'msg' => 'Plataforma creada correctamente.',
            'platform' => $platform
        ], 201);
    }

    /**
     * @Endpoint(description: "Actualiza los datos de una plataforma existente (solo administradores).")
     * @RequestBody(
     *     content: "application/json",
     *     example: {
     *         "name": "Shopify actualizado",
     *         "slug": "shopify-v2"
     *     }
     * )
     */
    public function update(Request $request, $id): JsonResponse
    {
        $platform = Platform::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'string|max:100',
            'slug' => 'string|max:100|unique:platforms,slug'
        ], [
            'name.string' => 'El nombre es obligatorio.',
            'slug.string' => 'El slug es obligatorio.',
            'slug.unique' => 'El slug ya existe.',
        ]);

        if (!isset($request->name) && !isset($request->slug)) {
            return response()->json([
                'msg' => 'Error en la petición',
                'errors' => 'Se debe introducir el nombre o el slug para editar.'
            ], 400);
        }

        if ($validator->fails()) {
            return response()->json([
                'msg' => 'Error en la petición',
                'errors' => $validator->errors()
            ], 400);
        }

        if ($request->name) {
            $platform->update(['name' => $request->name]);
        }

        if ($request->slug) {
            $platform->update(['slug' => $request->slug]);
        }

        return response()->json([
            'msg' => 'Plataforma actualizada correctamente.',
            'platform' => $platform
        ]);
    }

    /**
     * @Endpoint(description: "Elimina una plataforma por ID (solo administradores).")
     */
    public function delete($id): JsonResponse
    {
        $platform = Platform::findOrFail($id);
        $platform->delete();

        return response()->json(['message' => 'Plataforma eliminada.']);
    }
}
