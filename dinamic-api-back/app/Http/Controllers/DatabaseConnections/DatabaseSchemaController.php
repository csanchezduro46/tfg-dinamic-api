<?php

namespace App\Http\Controllers\DatabaseConnections;

use App\Http\Controllers\Controller;
use App\Models\DatabaseConnection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\ConnectionsDirect\DatabaseService;

class DatabaseSchemaController extends Controller
{
    private DatabaseService $dbService;

    public function __construct(DatabaseService $dbService)
    {
        $this->dbService = $dbService;
    }

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
