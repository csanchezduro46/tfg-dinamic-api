<?php

namespace App\Http\Controllers\Platforms;

use App\Models\Platform;
use App\Models\PlatformConnection;
use App\Models\PlatformNecessaryKey;
use App\Services\Platforms\PlatformServiceFactory;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class PlatformConnectionCredentialsController extends Controller
{
    // Claves de una conexion externa
    public function getByConnection($connectionId)
    {
        $connection = PlatformConnection::findOrFail($connectionId);

        return response()->json([
            'connection' => $connection->name,
            'connection_credentials' => $connection->credentials()->get()
        ]);
    }

    // Crear claves de una conexion externa
    public function storeKeys(Request $request, $id)
    {
        $connection = PlatformConnection::findOrFail($id);

        if ($connection->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            return response()->json(['msg' => 'Forbidden'], 403);
        }

        $necessaryKeys = $connection->version->platform->necessaryKeys;

        $errors = [];

        foreach ($necessaryKeys as $key) {
            if ($key->required && !$request->has($key->key)) {
                $errors[$key->key] = "El campo {$key->key} es obligatorio para esta plataforma.";
            }
        }

        if (!empty($errors)) {
            return response()->json(['msg' => 'Faltan campos obligatorios.', 'errors' => $errors], 400);
        }
        
        $connection->credentials()->delete();

        foreach ($necessaryKeys as $key) {
            if ($request->has($key->key)) {
                $connection->credentials()->create([
                    'necessary_key_id' => $key->id,
                    'value' => Crypt::encryptString($request->input($key->key)),
                ]);
            }
        }

        $result = $this->testInternal($connection);

        return $result !== true ? $result : response()->json([
            'msg' => 'Credenciales añadidas y conexión validada correctamente.',
            'connection' => $connection->fresh('credentials')
        ], 201);
    }


    // Eliminar claves de una conexion externa
    public function deleteAllKey($connectionId)
    {
        $connection = PlatformConnection::findOrFail($connectionId);
        $connection->credentials()->delete();

        return response()->json([
            'msg' => 'Claves eliminadas correctamente.'
        ]);
    }
    public function deleteKey($connectionId, $keyId)
    {
        $connection = PlatformConnection::findOrFail($connectionId);
        $key = $connection->credentials()->where('id', $keyId)->firstOrFail();

        $key->delete();

        return response()->json([
            'msg' => 'Clave eliminada correctamente.'
        ]);
    }

    public function testConnection($id)
    {
        $connection = PlatformConnection::findOrFail($id);

        if ($connection->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            return response()->json(['msg' => 'Forbidden'], 403);
        }

        // Recuperar claves actuales
        $credentials = [];
        foreach ($connection->version->platform->necessaryKeys as $key) {
            $cred = $connection->credentials()->where('necessary_key_id', $key->id)->first();
            if ($cred) {
                $credentials[$key->key] = $cred->value;
            }
        }

        if (empty($credentials)) {
            return response()->json([
                'msg' => 'No existen credenciales para la conexión indicada.'
            ], 404);
        }

        // Lanzar el test de conexión
        $service = PlatformServiceFactory::make($connection->version->platform->slug);
        $success = $service->testConnection($credentials, $connection->store_url, $connection->version->version);

        if (!$success) {
            return response()->json([
                'msg' => 'La conexión no ha podido ser validada con las credenciales actuales.'
            ], 422);
        }

        $connection->update([
            'status' => 'success',
            'last_checked_at' => now()
        ]);

        return response()->json([
            'msg' => 'Conexión validada correctamente.',
            'connection' => $connection->load('version.platform')
        ]);
    }


    private function testInternal(PlatformConnection $connection, array $credentials = []): \Illuminate\Http\JsonResponse|bool
    {
        $platformKeys = $connection->version->platform->necessaryKeys;

        // Si no se pasaron claves directamente, las recogemos de la base de datos
        if (empty($credentials)) {
            foreach ($platformKeys as $key) {
                $cred = $connection->credentials()->where('necessary_key_id', $key->id)->first();
                if ($cred) {
                    $credentials[$key->key] = $cred->value;
                }
            }
        }

        $service = PlatformServiceFactory::make($connection->version->platform->slug);
        $success = $service->testConnection($credentials, $connection->store_url, $connection->version->version);

        if (!$success) {
            return response()->json([
                'msg' => 'La conexión no ha podido ser validada con las credenciales proporcionadas.',
                'errors' => ['connection' => 'Verifica que las claves son correctas.']
            ], 422);
        }

        $connection->update([
            'status' => 'success',
            'last_checked_at' => now()
        ]);

        return true;
    }

}
