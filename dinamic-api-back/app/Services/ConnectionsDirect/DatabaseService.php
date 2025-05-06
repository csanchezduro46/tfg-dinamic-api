<?php

namespace App\Services\ConnectionsDirect;

use PDO;
use Exception;

class DatabaseService
{
    public function test(array $config): bool|string
    {
        try {
            $dsn = match ($config['driver']) {
                'mysql' => "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']}",
                'pgsql' => "pgsql:host={$config['host']};port={$config['port']};dbname={$config['database']}",
                'sqlsrv' => "sqlsrv:Server={$config['host']},{$config['port']};Database={$config['database']}",
                default => throw new Exception("Driver no soportado: {$config['driver']}")
            };

            $pdo = new PDO($dsn, $config['username'], $config['password']);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return true;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
