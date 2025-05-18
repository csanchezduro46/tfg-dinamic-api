<?php

namespace App\Http\Controllers\Platforms;

use App\Http\Controllers\Controller;
use App\Models\PlatformConnection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\RequestBody;

class PlatformConnectionController extends Controller
{
    /**
     * @Endpoint(description: "Devuelve todas las conexiones a plataformas (solo administradores).")
     */
    public function getAll()
    {
        return response()->json(PlatformConnection::with('version.platform', 'user')->get());
    }

    /**
     * @Endpoint(description: "Devuelve las conexiones de plataforma del usuario actual.")
     */
    public function getByUser()
    {
        return response()->json(Auth::user()->platformConnections()->with(['version.platform'])->get());
    }

    /**
     * @Endpoint(description: "Devuelve una única conexión de plataforma por ID (usuario propietario o admin).")
     */
    public function getSingle($id)
    {
        $connection = PlatformConnection::findOrFail($id);
        if ($connection->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403, 'Forbidden');
        }
        return response()->json($connection->load('version.platform', 'user'));

    }

    /**
     * @Endpoint(description: "Crea una nueva conexión con una plataforma externa.")
     * @RequestBody(
     *     content: "application/json",
     *     example: {
     *         "platform_version_id": 1,
     *         "name": "Tienda Demo",
     *         "store_url": "mi-tienda-demo",
     *         "config": {
     *             "extra_param": "valor"
     *         }
     *     }
     * )
     */
    public function store(Request $request)
    {
        if (Auth::user()->hasRole('admin')) {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|integer|exists:users,id',
                'platform_version_id' => 'required|integer|exists:platform_versions,id',
                'store_url' => 'required|string|max:150',
                'name' => 'required|string|max:100',
                'config' => 'nullable|array',
            ], [
                'platform_version_id.integer' => 'La versión de la plataforma no es válida.',
                'platform_version_id.exists' => 'La versión de la plataforma no existe.',
                'user_id.required' => 'El usuario es obligatorio.',
                'user_id.exists' => 'El usuario no existe.',
                'platform_version_id.required' => 'La versión de la plataforma es obligatoria.',
                'store_url.required' => 'La url de la tienda es obligatoria, debe ser el slug de la url.',
                'name.required' => 'El nombre es obligatorio.',
                'name.max' => 'El nombre no puede tener más de 100 caracteres.',
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'platform_version_id' => 'required|integer|exists:platform_versions,id',
                'store_url' => 'required|string|max:150',
                'name' => 'required|string|max:100',
                'config' => 'nullable|array',
            ], [
                'platform_version_id.integer' => 'La versión de la plataforma no es válida.',
                'platform_version_id.exists' => 'La versión de la plataforma no existe.',
                'platform_version_id.required' => 'La versión de la plataforma es obligatoria.',
                'store_url.required' => 'La url de la tienda es obligatoria, debe ser el slug de la url.',
                'name.required' => 'El nombre es obligatorio.',
                'name.max' => 'El nombre no puede tener más de 100 caracteres.',
            ]);
        }

        if ($validator->fails()) {
            return response()->json([
                'msg' => 'Error en la petición',
                'errors' => $validator->errors()
            ], 400);
        }

        $validated = $validator->validated();
        if (!Auth::user()->hasRole('admin')) {
            $validated['user_id'] = Auth::id();
        }
        $connection = PlatformConnection::create($validated)->load('version.platform', 'user');

        return response()->json([
            'msg' => 'Conexión creada correctamente.',
            'connection' => $connection
        ], 201);
    }

    /**
     * @Endpoint(description: "Actualiza el nombre, URL o configuración de una conexión de plataforma.")
     * @RequestBody(
     *     content: "application/json",
     *     example: {
     *         "name": "Tienda actualizada",
     *         "store_url": "mi-nueva-tienda"
     *     }
     * )
     */
    public function update(Request $request, $id)
    {
        $connection = PlatformConnection::findOrFail($id);
        if ($connection->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403, 'Forbidden');
        }

        if (Auth::user()->hasRole('admin')) {
            $validator = Validator::make($request->all(), [
                'user_id' => 'sometimes|integer|exists:users,id',
                'name' => 'sometimes|string|max:100',
                'store_url' => 'sometimes|string|max:150',
                'config' => 'nullable|array',
            ], [
                'platform_version_id' => 'La versión de la plataforma no puede cambiarse, solo puedes eliminar la conexión y crear una nueva.',
                'store_url.string' => 'La url de la tienda es obligatoria, debe ser el slug de la url.',
                'name.max' => 'El nombre no puede tener más de 100 caracteres.',
                'user_id.exists' => 'El usuario no existe.'
            ]);
            $rules['user_id'] = 'sometimes|integer|exists:users,id';
        } else {
            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|string|max:100',
                'store_url' => 'sometimes|string|max:150',
                'config' => 'nullable|array',
            ], [
                'platform_version_id' => 'La versión de la plataforma no puede cambiarse, solo puedes eliminar la conexión y crear una nueva.',
                'store_url.string' => 'La url de la tienda es obligatoria, debe ser el slug de la url.',
                'name.max' => 'El nombre no puede tener más de 100 caracteres.',
            ]);
        }

        if ($validator->fails()) {
            return response()->json([
                'msg' => 'Error en la petición',
                'errors' => $validator->errors()
            ], 400);
        }

        $validated = $validator->validated();
        $connection->update($validated);
        $connection->load('version.platform');

        return response()->json([
            'msg' => 'Conexión actualizada correctamente.',
            'connection' => $connection
        ]);
    }

    /**
     * @Endpoint(description: "Elimina una conexión de plataforma y sus credenciales asociadas.")
     */
    public function delete($id)
    {
        $connection = PlatformConnection::findOrFail($id);
        if (!Auth::user()->hasRole('admin') && $connection->user->id != Auth::id()) {
            return response()->json([
                'msg' => 'Forbidden',
            ], 403);
        }
        $connection->credentials()->delete();
        $connection->delete();

        return response()->json([
            'msg' => 'Conexión eliminada correctamente.'
        ]);
    }
}