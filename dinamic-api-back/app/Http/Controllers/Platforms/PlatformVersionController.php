<?php

namespace App\Http\Controllers\Platforms;

use App\Http\Controllers\Controller;
use App\Models\PlatformVersion;
use App\Models\Platform;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\RequestBody;

class PlatformVersionController extends Controller
{
    /**
     * @Endpoint(description: "Obtiene todas las versiones de plataforma (opcionalmente filtradas por versión o plataforma_id). Solo accesible por administradores.")
     */
    public function getAll(Request $request): JsonResponse
    {
        $query = PlatformVersion::with('platform');

        if ($request->filled('version')) {
            $query->where('version', 'like', '%' . $request->version . '%');
        }

        if ($request->filled('platform_id')) {
            $query->where('platform_id', $request->platform_id);
        }

        return response()->json($query->get());
    }

    /**
     * @Endpoint(description: "Obtiene todas las versiones disponibles para una plataforma concreta.")
     */
    public function getByPlatform($id): JsonResponse
    {
        $platform = Platform::findOrFail($id);
        $versions = PlatformVersion::where('platform_id', $platform->id)->get();

        return response()->json($versions);
    }

    /**
     * @Endpoint(description: "Crea una nueva versión para una plataforma específica (solo administradores).")
     * @RequestBody(
     *     content: "application/json",
     *     example: {
     *         "version": "2024-10",
     *         "description": "Versión octubre 2024 de Shopify"
     *     }
     * )
     */
    public function store(Request $request, $id): JsonResponse
    {
        $platform = Platform::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'version' => 'required|string|max:50|unique:platform_versions,version,NULL,id,platform_id,' . $platform->id,
            'description' => 'nullable|string|max:255',
        ], [
            'platform_id.required' => 'La plataforma es obligatoria.',
            'platform_id.exists' => 'La plataforma seleccionada no es válida.',
            'version.required' => 'La versión es obligatoria.',
            'version.unique' => 'Ya existe esa versión para la plataforma seleccionada.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'msg' => 'Error en la petición',
                'errors' => $validator->errors()
            ], 400);
        }

        $validated = $validator->validated();
        $version = $platform->versions()->create($validated);

        return response()->json([
            'msg' => 'Versión creada correctamente.',
            'version' => $version
        ], 201);
    }

    /**
     * @Endpoint(description: "Actualiza una versión existente (nombre de versión o descripción).")
     * @RequestBody(
     *     content: "application/json",
     *     example: {
     *         "version": "2024-11",
     *         "description": "Actualización de seguridad"
     *     }
     * )
     */
    public function update(Request $request, $id): JsonResponse
    {
        $version = PlatformVersion::findOrFail($id);
        if (!$request->filled('version') && !$request->filled('description')) {
            return response()->json([
                'msg' => 'Debes introducir al menos la versión o descripción para actualizar.'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'version' => [
                'string',
                'max:50',
                Rule::unique('platform_versions', 'version')
                    ->ignore($version->id)
                    ->where('platform_id', $version->platform_id)
            ],
            'description' => 'nullable|string|max:255',
        ], [
            'version.unique' => 'Ya existe esa versión para la plataforma seleccionada.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'msg' => 'Error en la petición',
                'errors' => $validator->errors()
            ], 400);
        }
        $validated = $validator->validated();
        $version->update($validated);

        return response()->json([
            'msg' => 'Versión actualizada correctamente.',
            'version' => $version
        ]);
    }

    /**
     * @Endpoint(description: "Elimina una versión de plataforma existente (solo administradores).")
     */
    public function delete($id): JsonResponse
    {
        $version = PlatformVersion::findOrFail($id);
        $version->delete();

        return response()->json(['msg' => 'Versión eliminada.']);
    }
}
