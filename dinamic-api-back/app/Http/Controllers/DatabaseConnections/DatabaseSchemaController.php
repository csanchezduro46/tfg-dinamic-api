<?php

namespace App\Http\Controllers\DatabaseConnections;

use App\Http\Controllers\Controller;
use App\Models\DatabaseConnection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\ConnectionsDirect\DatabaseService;
use Dedoc\Scramble\Attributes\Endpoint;

class DatabaseSchemaController extends Controller
{
    private DatabaseService $dbService;

    public function __construct(DatabaseService $dbService)
    {
        $this->dbService = $dbService;
    }

    /**
     * @Endpoint(description: "Obtiene el esquema completo de una base de datos (todas las tablas y sus columnas)")
     */
    public function getFullSchema($id)
    {
        $connection = DatabaseConnection::where('id', $id)->firstOrFail();
        if ($connection->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403, 'Forbidden');
        }

        $tables = $this->dbService->getTables($connection);

        $schema = [];

        foreach ($tables as $table) {
            $schema[$table] = $this->dbService->getColumns($connection, $table);
        }

        return response()->json([
            'schema' => $schema
        ]);
    }

    /**
     * @Endpoint(description: "Devuelve el listado de tablas de una conexión de base de datos")
     */
    public function getTables($id)
    {
        $connection = DatabaseConnection::where('id', $id)->firstOrFail();
        if ($connection->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403, 'Forbidden');
        }

        return response()->json([
            'tables' => $this->dbService->getTables($connection)
        ]);
    }

    /**
     * @Endpoint(description: "Devuelve las columnas de una tabla concreta de la base de datos")
     */
    public function getColumns($id, $table)
    {
        $connection = DatabaseConnection::where('id', $id)->firstOrFail();
        if ($connection->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403, 'Forbidden');
        }

        $columns = $this->dbService->getColumns($connection, $table);
        if (empty($columns)) {
            return response()->json([
                'msg' => 'La tabla no existe o no tiene columnas accesibles',
                'columns' => []
            ], 404);
        }

        return response()->json([
            'table' => $table,
            'columns' => $columns
        ]);
    }
}
