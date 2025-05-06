<?php

namespace App\Services\ConnectionsDirect;

use App\Models\DatabaseConnection;
use PDO;
use Exception;
use Illuminate\Support\Facades\Cache;

class DatabaseService
{
    private function getPDO(array $config): PDO
    {
        $dsn = match ($config['driver']) {
            'mysql' => "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']}",
            'pgsql' => "pgsql:host={$config['host']};port={$config['port']};dbname={$config['database']}",
            'sqlsrv' => "sqlsrv:Server={$config['host']},{$config['port']};Database={$config['database']}",
            default => throw new Exception("Driver no soportado: {$config['driver']}")
        };

        return new PDO($dsn, $config['username'], $config['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
    }

    public function test(array $config): bool|string
    {
        try {
            $pdo = $this->getPDO($config);
            $pdo->query('SELECT 1');
            return true;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function getTables(DatabaseConnection $connection): array
    {
        return Cache::remember("db:{$connection->id}:tables", 3600, function () use ($connection) {
            $pdo = $this->getPDO($connection->getDecryptedCredentials());

            return match ($connection->driver) {
                'mysql', 'pgsql' => $pdo->query("
                    SELECT table_name
                    FROM information_schema.tables
                    WHERE table_schema = 'public'
                ")->fetchAll(PDO::FETCH_COLUMN),
                'sqlsrv' => $pdo->query("SELECT name FROM sys.tables")->fetchAll(PDO::FETCH_COLUMN),
                default => []
            };
        });
    }

    public function getColumns(DatabaseConnection $connection, string $table): array
    {
        return Cache::remember("db:{$connection->id}:table:{$table}:columns", 3600, function () use ($connection, $table) {
            $pdo = $this->getPDO($connection->getDecryptedCredentials());

            return match ($connection->driver) {
                'mysql', 'pgsql' => $pdo->query("
                    SELECT column_name, data_type, is_nullable, column_default
                    FROM information_schema.columns
                    WHERE table_name = '{$table}'
                ")->fetchAll(PDO::FETCH_ASSOC),
                'sqlsrv' => $pdo->query("
                    SELECT COLUMN_NAME as column_name, DATA_TYPE as data_type
                    FROM INFORMATION_SCHEMA.COLUMNS
                    WHERE TABLE_NAME = '{$table}'
                ")->fetchAll(PDO::FETCH_ASSOC),
                default => []
            };
        });
    }
}
