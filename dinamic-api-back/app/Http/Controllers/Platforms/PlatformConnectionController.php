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
        return response()->json(PlatformConnection::all());
    }

    public function getByUser()
    {
        return response()->json(Auth::user()->platformConnections()->with('platform')->get());
    }

    public function store(Request $request)
    {
        $rules = [
            'platform_id' => 'required',
            'platform_version_id' => 'required',
            'store_url' => 'required|string|max:150',
            'name' => 'required|string|max:100',
            'config' => 'nullable|array',
        ];

        if (Auth::user()->hasRole('admin')) {
            $rules['user_id'] = 'required';
        }

        $validator = Validator::make($request->all(), $rules, [
            'platform_id.required' => 'La plataforma es obligatoria.',
            'user_id.required' => 'El usuario es obligatorio.',
            'platform_version_id.required' => 'La versión de la plataforma es obligatoria.',
            'store_url.required' => 'La url de la tienda es obligatoria, debe ser el slug de la url.',
            'name.required' => 'El nombre es obligatorio.',
            'name.max' => 'El nombre no puede tener más de 100 caracteres.',
        ]);

        $platform = Platform::findOrFail($request->platform_id);
        $necessaryKeys = $platform->necessaryKeys;
        $errors = [];

        foreach ($necessaryKeys as $key) {
            if ($key->required && !$request->has($key->key)) {
                $errors[$key->key] = "El campo {$key->key} es obligatorio para esta plataforma.";
            }
        }

        $validator->after(function ($validator) use ($request) {
            if ($request->platform_id && $request->platform_version_id) {
                $valid = PlatformVersion::where('id', $request->platform_version_id)
                    ->where('platform_id', $request->platform_id)
                    ->exists();

                if (!$valid) {
                    $validator->errors()->add('platform_version_id', 'La versión seleccionada no pertenece a la plataforma elegida.');
                }
            }
        });

        if ($validator->fails()) {
            $errors[] = $validator->errors();
        }

        if (!empty($errors)) {
            return response()->json([
                'msg' => 'Error en la petición',
                'errors' => $errors
            ], 400);
        }

        $validated = $validator->validated();
        if (!Auth::user()->hasRole('admin')) {
            $validated['user_id'] = Auth::id();
        }
        $connection = PlatformConnection::create($validated);

        foreach ($necessaryKeys as $key) {
            $connection->credential()->create([
                'necessary_key_id' => $key->id,
                'value' => $request->input($key->key),
            ]);
        }

        $result = $this->handleCredentialsUpdate($request, $connection);
        return response()->json([
            'msg' => $result !== true ? 'Error al validar la conexión' : 'Conexión creada correctamente.',
            'connection' => $connection,
            'errors' => $result !== true ? ($result->original['errors'] ?? null) : null
        ], $result !== true ? 422 : 201);
    }

    public function update(Request $request, $id)
    {
        $connection = PlatformConnection::findOrFail($id);
        if ($connection->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403, 'Forbidden');
        }

        $rules = [
            'name' => 'sometimes|string|max:100',
            'platform_version_id' => 'sometimes',
            'store_url' => 'sometimes|string|max:150',
            'config' => 'nullable|array',
        ];

        $validator = Validator::make($request->all(), $rules, [
            'name.max' => 'El nombre no puede tener más de 100 caracteres.',
            'platform_version_id.required' => 'La versión de la plataforma es obligatoria.',
            'store_url.required' => 'La url de la tienda es obligatoria, debe ser el slug de la url.',
            'name.required' => 'El nombre es obligatorio.',
        ]);

        $payloadValidate = [
            'version' => $request->platform_version_id ?? $connection->platform_version_id,
            'platform' => $connection->version->platform_id
        ];

        $validator->after(function ($validator) use ($payloadValidate) {
            if ($payloadValidate['version']) {
                $valid = PlatformVersion::where('id', $payloadValidate['version'])
                    ->where('platform_id', $payloadValidate['platform'])
                    ->exists();

                if (!$valid) {
                    $validator->errors()->add('platform_version_id', 'La versión seleccionada no pertenece a la plataforma elegida.');
                }
            }
        });

        if ($validator->fails()) {
            return response()->json([
                'msg' => 'Error en la petición',
                'errors' => $validator->errors()
            ], 400);
        }

        $result = $this->handleCredentialsUpdate($request, $connection);

        if ($result !== true) {
            return $result; // Es un response 422 si falló el test
        }

        $validated = $validator->validated();
        $connection->update($validated);

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

    private function handleCredentialsUpdate(Request $request, PlatformConnection $connection)
    {
        $updatedCredentials = false;
        $platformNecessaryKeys = $connection->platform->necessaryKeys;

        $credentials = [];

        foreach ($platformNecessaryKeys as $necessaryKey) {
            if ($request->has($necessaryKey->key)) {
                $credential = $connection->credentials()
                    ->where('necessary_key_id', $necessaryKey->id)
                    ->first();

                if ($credential) {
                    $credential->update([
                        'value' => $request->input($necessaryKey->key)
                    ]);
                } else {
                    $connection->credential()->create([
                        'necessary_key_id' => $necessaryKey->id,
                        'value' => $request->input($necessaryKey->key)
                    ]);
                }

                $credentials[$necessaryKey->key] = $request->input($necessaryKey->key);
                $updatedCredentials = true;
            }
        }

        if ($updatedCredentials && $connection->status !== 'success') {
            $service = PlatformServiceFactory::make($connection->platform->slug);

            $connectionSuccessful = $service->testConnection(
                $credentials,
                $connection->store_url,
                $connection->version->version
            );

            if (!$connectionSuccessful) {
                return response()->json([
                    'msg' => 'La conexión no se ha podido validar, comprueba las credenciales.',
                ], 422);
            }

            $connection->update([
                'status' => 'success',
                'last_checked_at' => now()
            ]);
        }

        return true;
    }
}