<?php

namespace App\Http\Controllers\Platforms;

use App\Http\Controllers\Controller;
use App\Models\Platform;
use App\Models\PlatformConnection;
use App\Models\PlatformVersion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Services\Platforms\PlatformServiceFactory;

class PlatformConnectionController extends Controller
{
    public function getAll()
    {
        return response()->json(PlatformConnection::with('version.platform')->get());
    }

    public function getByUser()
    {
        return response()->json(Auth::user()->platformConnections()->with(['version.platform'])->get());
    }

    public function getSingle($id) 
    {
        $connection = PlatformConnection::findOrFail($id);
        if ($connection->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403, 'Forbidden');
        }
        return response()->json($connection->with(['version.platform'])->get());

    }

    public function store(Request $request)
    {
        $rules = [
            'platform_version_id' => 'required|integer|exists:platform_versions,id',
            'store_url' => 'required|string|max:150',
            'name' => 'required|string|max:100',
            'config' => 'nullable|array',
        ];

        if (Auth::user()->hasRole('admin')) {
            $rules['user_id'] = 'required|integer|exists:users,id';
        }

        $validator = Validator::make($request->all(), $rules, [
            'platform_version_id.integer' => 'La versión de la plataforma no es válida.',
            'platform_version_id.exists' => 'La versión de la plataforma no existe.',
            'user_id.required' => 'El usuario es obligatorio.',
            'user_id.exists' => 'El usuario no existe.',
            'platform_version_id.required' => 'La versión de la plataforma es obligatoria.',
            'store_url.required' => 'La url de la tienda es obligatoria, debe ser el slug de la url.',
            'name.required' => 'El nombre es obligatorio.',
            'name.max' => 'El nombre no puede tener más de 100 caracteres.',
        ]);

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
        $connection = PlatformConnection::create($validated)->load('version.platform');

        return response()->json([
            'msg' => 'Conexión creada correctamente.',
            'connection' => $connection
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $connection = PlatformConnection::findOrFail($id);
        if ($connection->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403, 'Forbidden');
        }

        $rules = [
            'name' => 'sometimes|string|max:100',
            'store_url' => 'sometimes|string|max:150',
            'config' => 'nullable|array',
        ];

        if (Auth::user()->hasRole('admin')) {
            $rules['user_id'] = 'sometimes|integer|exists:users,id';
        }

        $validator = Validator::make($request->all(), $rules, [
            'platform_version_id' => 'La versión de la plataforma no puede cambiarse, solo puedes eliminar la conexión y crear una nueva.',
            'store_url.string' => 'La url de la tienda es obligatoria, debe ser el slug de la url.',
            'name.max' => 'El nombre no puede tener más de 100 caracteres.',
            'user_id.exists' => 'El usuario no existe.'
        ]);

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