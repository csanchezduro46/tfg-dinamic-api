<?php

namespace App\Http\Controllers\Platforms;

use App\Models\Platform;
use App\Models\PlatformNecessaryKey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\RequestBody;

class PlatformNecessaryKeysController extends Controller
{
    /**
     * @Endpoint(description: "Devuelve las claves necesarias de una plataforma (access_token, api_key, etc.)")
     */
    public function getKeysPlatform($platformId)
    {
        $platform = Platform::findOrFail($platformId);

        return response()->json([
            'platform' => $platform->name,
            'necessary_keys' => $platform->necessaryKeys()->get()
        ]);
    }

    /**
     * @Endpoint(description: "Crea una nueva clave necesaria para una plataforma (solo administradores).")
     * @RequestBody(
     *     content: "application/json",
     *     example: {
     *         "key": "access_token",
     *         "label": "Access Token",
     *         "required": true
     *     }
     * )
     */
    public function store(Request $request, $platformId)
    {
        $platform = Platform::findOrFail($platformId);

        $validator = Validator::make($request->all(), [
            'key' => 'required|string|max:100|unique:platform_necessary_keys,key',
            'label' => 'required|string|max:255',
            'required' => 'boolean'
        ], [
            'key.required' => 'La key es obligatoria.',
            'label.required' => 'El label es obligatorio.',
            'key.string' => 'La key no es correcta.',
            'label.string' => 'El label no es correcto.',
            'key.unique' => 'Ya existe esa key para la plataforma seleccionada.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'msg' => 'Error de validación',
                'errors' => $validator->errors()
            ], 400);
        }

        $data = $validator->validated();
        $data['platform_id'] = $platform->id;

        $key = PlatformNecessaryKey::create($data);

        return response()->json([
            'msg' => 'Clave necesaria creada correctamente.',
            'key' => $key
        ], 201);
    }

    /**
     * @Endpoint(description: "Actualiza una clave necesaria de una plataforma.")
     * @RequestBody(
     *     content: "application/json",
     *     example: {
     *         "label": "Token de acceso",
     *         "required": false
     *     }
     * )
     */
    public function update(Request $request, $platformId, $keyId)
    {
        $platform = Platform::findOrFail($platformId);
        $key = $platform->necessaryKeys()->where('id', $keyId)->firstOrFail();

        $validator = Validator::make($request->all(), [
            'key' => 'sometimes|string|max:100|unique:platform_necessary_keys,key',
            'label' => 'sometimes|string|max:255',
            'required' => 'boolean'
        ], [
            'key.unique' => 'Ya existe esa key para la plataforma seleccionada.',
            'key.string' => 'La key no es correcta.',
            'label.string' => 'El label no es correcto.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'msg' => 'Error de validación',
                'errors' => $validator->errors()
            ], 400);
        }

        $key->update($validator->validated());

        return response()->json([
            'msg' => 'Clave necesaria actualizada correctamente.',
            'key' => $key
        ]);
    }

    /**
     * @Endpoint(description: "Elimina una clave necesaria de una plataforma.")
     */
    public function delete($platformId, $keyId)
    {
        $platform = Platform::findOrFail($platformId);
        $key = $platform->necessaryKeys()->where('id', $keyId)->firstOrFail();

        $key->delete();

        return response()->json([
            'msg' => 'Clave necesaria eliminada correctamente.'
        ]);
    }
}
