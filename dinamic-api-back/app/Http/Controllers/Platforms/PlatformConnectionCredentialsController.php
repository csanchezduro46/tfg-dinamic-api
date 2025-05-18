<?php

namespace App\Http\Controllers\Platforms;

use App\Models\PlatformConnection;
use App\Services\Platforms\PlatformServiceFactory;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use App\Http\Controllers\Controller;
use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\RequestBody;

class PlatformConnectionCredentialsController extends Controller
{
    /**
     * @Endpoint(description: "Devuelve las claves necesarias de una conexión externa por ID")
     */
    public function getByConnection($connectionId)
    {
        $connection = PlatformConnection::findOrFail($connectionId);

        return response()->json([
            'connection' => $connection->name,
            'connection_credentials' => $connection->credentials()->with('necessaryKey')->get()
        ]);
    }

    /**
     * @Endpoint(description: "Guarda y valida las claves necesarias para una conexión externa")
     * @RequestBody(
     *     content: "application/json",
     *     example: {
     *         "credential": "shpat_xxxxxxxx"
     *     }
     * )
     */
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


    /**
     * @Endpoint(description: "Elimina todas las claves asociadas a una conexión externa")
     */
    public function deleteAllKey($connectionId)
    {
        $connection = PlatformConnection::findOrFail($connectionId);
        $connection->credentials()->delete();

        return response()->json([
            'msg' => 'Claves eliminadas correctamente.'
        ]);
    }

    /**
     * @Endpoint(description: "Elimina una clave específica de una conexión externa")
     */
    public function deleteKey($connectionId, $keyId)
    {
        $connection = PlatformConnection::findOrFail($connectionId);
        $key = $connection->credentials()->where('id', $keyId)->firstOrFail();

        $key->delete();

        return response()->json([
            'msg' => 'Clave eliminada correctamente.'
        ]);
    }

    /**
     * @Endpoint(description: "Testea la conexión a una plataforma usando las credenciales almacenadas")
     */
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
        ], 200);
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
