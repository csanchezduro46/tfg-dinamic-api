<?php

namespace App\Http\Controllers\DatabaseConnections;

use App\Http\Controllers\Controller;
use App\Models\DatabaseConnection;
use App\Services\ConnectionsDirect\DatabaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\RequestBody;

class DatabaseConnectionController extends Controller
{
    private DatabaseService $service;

    public function __construct(DatabaseService $databaseService)
    {
        $this->service = $databaseService;
    }

    /**
     * @Endpoint(description: "Devuelve todas las conexiones de base de datos registradas en el sistema (admin)")
     */
    public function getAll()
    {
        return response()->json(DatabaseConnection::with('user')->get());
    }

    /**
     * @Endpoint(description: "Devuelve todas las conexiones asociadas al usuario actual")
     */
    public function getConnectionsUser()
    {
        return response()->json(Auth::user()->databaseConnections);
    }

    /**
     * @Endpoint(description: "Devuelve una conexión concreta por ID si pertenece al usuario actual o es admin")
     */
    public function getConnectionBd($id)
    {
        $connection = DatabaseConnection::findOrFail($id);
        if ($connection->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403, 'Forbidden');
        }
        return response()->json($connection);
    }

    /**
     * @Endpoint(description: "Crea una nueva conexión a base de datos")
     * @RequestBody(
     *     content: "application/json",
     *     example: {
     *         "name": "Conexión PostgreSQL",
     *         "driver": "pgsql",
     *         "host": "localhost",
     *         "port": 5432,
     *         "database": "mi_base_datos",
     *         "username": "usuario_db",
     *         "password": "secreto123"
     *     }
     * )
     */
    public function store(Request $request)
    {
        if (Auth::user()->hasRole('admin')) {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|integer|exists:users,id',
                'name' => 'required|string|max:100',
                'driver' => 'required|in:mysql,pgsql,sqlsrv',
                'host' => 'required|string',
                'port' => 'required|integer',
                'database' => 'required|string',
                'username' => 'required|string',
                'password' => 'required|string',
            ], [
                'user_id.required' => 'Es obligatorio introducir el usuario al que irá asociada la conexión.',
                'name.required' => 'El nombre es obligatorio.',
                'driver.required' => 'Es obligatorio indicar el tipo de bbdd: mysql, pgsql o sqlsrv.',
                'host.required' => 'El host es obligatorio.',
                'port.required' => 'El puerto es obligatorio.',
                'database.required' => 'El nombre de la base de datos es obligatorio.',
                'username.required' => 'El usuario es obligatorio.',
                'password.required' => 'La contraseña es obligatoria.',
                'port.integer' => 'El puerto no es válido, debe ser de tipo numérico.'
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:100',
                'driver' => 'required|in:mysql,pgsql,sqlsrv',
                'host' => 'required|string',
                'port' => 'required|integer',
                'database' => 'required|string',
                'username' => 'required|string',
                'password' => 'required|string',
            ], [
                'name.required' => 'El nombre es obligatorio.',
                'driver.required' => 'Es obligatorio indicar el tipo de bbdd: mysql, pgsql o sqlsrv.',
                'host.required' => 'El host es obligatorio.',
                'port.required' => 'El puerto es obligatorio.',
                'database.required' => 'El nombre de la base de datos es obligatorio.',
                'username.required' => 'El usuario es obligatorio.',
                'password.required' => 'La contraseña es obligatoria.',
                'port.integer' => 'El puerto no es válido, debe ser de tipo numérico.'
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

        $validated['username'] = Crypt::encryptString($validated['username']);
        $validated['password'] = Crypt::encryptString($validated['password']);
        $validated['status'] = 'pending';
        $connection = DatabaseConnection::create($validated);

        $result = $this->test($connection, true);

        return $result !== true ? $result : response()->json([
            'msg' => 'Conexión guardada correctamente',
            'connection' => $connection
        ], 201);
    }

    /**
     * @Endpoint(description: "Actualiza los datos de una conexión existente")
     * @RequestBody(
     *     content: "application/json",
     *     example: {
     *         "name": "Conexión actualizada",
     *         "host": "127.0.0.1",
     *         "username": "nuevo_usuario"
     *     }
     * )
     */
    public function update(Request $request, $id)
    {
        $connection = DatabaseConnection::where('id', $id)->firstOrFail();
        if ($connection->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403, 'Forbidden');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:100',
            'host' => 'sometimes|string',
            'port' => 'sometimes|integer',
            'database' => 'sometimes|string',
            'username' => 'sometimes|string',
            'password' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'msg' => 'Error en la petición',
                'errors' => $validator->errors()
            ], 400);
        }

        $data = $validator->validated();

        if (isset($data['username'])) {
            $data['username'] = Crypt::encryptString($data['username']);
        }

        if (isset($data['password'])) {
            $data['password'] = Crypt::encryptString($data['password']);
        }

        $data['status'] = 'pending';
        $connection->update($data);

        $result = $this->test($connection, false);

        return $result !== true ? $result : response()->json([
            'msg' => 'Conexión actualizada correctamente',
            'connection' => $connection
        ], 201);
    }

    /**
     * @Endpoint(description: "Elimina una conexión de base de datos")
     */
    public function delete($id)
    {
        $connection = DatabaseConnection::where('id', $id)->firstOrFail();
        if ($connection->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403, 'Forbidden');
        }

        $connection->delete();

        return response()->json(['msg' => 'Conexión eliminada']);
    }

    /**
     * @Endpoint(description: "Lanza un test de conexión a la base de datos seleccionada")
     */
    public function testConnection($id)
    {
        $connection = DatabaseConnection::where('id', $id)->firstOrFail();
        if ($connection->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403, 'Forbidden');
        }

        // Lanzar el test de conexión
        $result = $this->service->test($connection->getDecryptedCredentials());
        $status = $result !== true ? 'failed' : 'success';
        $connection->update(['status' => $status]);

        $response = $result !== true ? ['msg' => $result] : [
            'msg' => 'Conexión validada correctamente',
            'connection' => $connection
        ];

        return response()->json($response, $result !== true ? 422 : 200);
    }

    public function test(DatabaseConnection $connection, $create)
    {
        $result = $this->service->test($connection->getDecryptedCredentials());
        $response = $create ? 'Conexión creada correctamente' : 'Conexión actualizada correctamente';

        if ($result === true) {
            $connection->update(['status' => 'success']);
            return response()->json(['msg' => $response, 'connection' => $connection]);
        }

        $connection->update(['status' => 'failed']);

        return response()->json([
            'msg' => 'Error en la conexión',
            'error' => $result
        ], 422);
    }
}
