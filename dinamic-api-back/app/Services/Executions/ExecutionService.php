<?php

namespace App\Services\Executions;

use App\Models\Execution;
use App\Models\PlatformConnection;
use App\Services\Platforms\PlatformServiceFactory;
use Illuminate\Support\Facades\Log;
use App\Services\ConnectionsDirect\DatabaseService;
use Illuminate\Support\Facades\Http;

class ExecutionService
{
    protected DatabaseService $dbService;
    protected PlatformServiceFactory $platformService;

    public function __construct(DatabaseService $dbService, PlatformServiceFactory $platformService)
    {
        $this->dbService = $dbService;
        $this->platformService = $platformService;
    }

    public function runExecution(Execution $execution)
    {
        $mapping = $execution->mapping;

        $execution->update(['status' => 'running']);

        $launch = $execution->history()->create([
            'status' => 'running',
            'launched_at' => now(),
        ]);

        try {
            // Comprobar las tablas relacionadas con los datos reales de la BBDD si existe
            $rows = [];
            if ($mapping->source_db_connection_id && $mapping->source_table) {
                $rows = $this->getRowsFromDb($mapping);
            }

            // Convertir a payload usando los campos mapeados
            $payloads = [];
            $responses = [];
            $status = 'success';

            foreach ($rows as $row) {
                $payload = $this->mapFieldsToPayload($mapping->fields, $row);
                $payloads[] = $payload;
            }

            if ($mapping->target_api_call_id) {
                $responses = $this->sendToExternalApi($mapping, $payloads);
            } elseif ($mapping->target_db_connection_id && $mapping->target_table) {
                $responses = $this->insertIntoDatabase($mapping, $payloads);
            } else {
                $status = 'failed';
                $responses = 'No se ha definido un destino válido para este mapeo.';
            }

            $execution->update([
                'status' => $status,
                'finished_at' => now(),
                'last_executed_at' => now(),
                'response_log' => [
                    'rows_sent' => count($payloads),
                    'results_errors' => $responses
                ]
            ]);

            $launch->update([
                'status' => $status,
                'log' => [
                    'payloads_sent' => $payloads,
                    'responses' => $responses
                ]
            ]);

            return ["response" => $execution['response_log'], "status" => $status];

        } catch (\Throwable $e) {
            $execution->update([
                'status' => 'failed',
                'finished_at' => now(),
                'last_executed_at' => now(),
                'response_log' => ['error' => $e->getMessage()]
            ]);

            $launch->update([
                'status' => 'failed',
                'log' => ['error' => $e->getMessage()]
            ]);

            $msg = "Error en ejecución #{$execution->id}: {$e->getMessage()}";

            Log::error($msg);
            return ["response" => $msg, "status" => 'failed'];
        }
    }

    private function getRowsFromDb($mapping): array
    {
        $connection = $mapping->sourceDb;
        $table = $mapping->source_table;

        $columns = $mapping->fields->pluck('source_field')->unique()->toArray();

        return $this->dbService->getData($connection, $table, $columns);
    }

    private function mapFieldsToPayload($fields, $row): array
    {
        $payload = [];

        foreach ($fields as $field) {
            $value = $row[$field->source_field] ?? null;
            data_set($payload, $field->target_field, $value);
        }

        return $payload;
    }

    private function sendToExternalApi($mapping, array $payloads): array
    {
        $results = [];

        $apiCall = $mapping->targetApiCall;

        $version = $apiCall->version;
        $platformSlug = $version->platform->slug;
        $connection = PlatformConnection::where('platform_version_id', '=', $version->id)->where('user_id', '=', $mapping->user->id)->firstOrFail();

        $platformService = $this->platformService::make($platformSlug);

        foreach ($payloads as $payload) {
            try {
                $response = $platformService->sendApiCall($apiCall, $connection, $payload);
                if ($response->getStatusCode() != 200 && $response != 201) {
                    $results[] = [
                        'status' => $response->getStatusCode(),
                        'payload' => $payload,
                        'response' => $response->json(),
                    ];
                }
            } catch (\Throwable $e) {
                $results[] = [
                    'error' => $e->getMessage(),
                    'payload' => $payload
                ];
            }
        }

        return $results;
    }


    private function insertIntoDatabase($mapping, array $payloads): array
    {
        $connection = $mapping->targetDb;
        $table = $mapping->target_table;
        $columnsInfo = $this->dbService->getColumns($connection, $table);

        $columnTypes = collect($columnsInfo)->mapWithKeys(fn($col) => [
            $col['column_name'] => $col['data_type']
        ])->toArray();

        $inserted = [];

        foreach ($payloads as $payload) {
            $flatRow = [];

            foreach ($mapping->fields as $field) {
                $dbColumn = $field->target_field;
                $value = data_get($payload, $field->target_field);

                if (isset($columnTypes[$dbColumn])) {
                    $flatRow[$dbColumn] = $this->castValue($value, $columnTypes[$dbColumn]);
                }
            }

            try {
                $this->dbService->insert($connection, $table, $flatRow);
                $inserted[] = $flatRow;
            } catch (\Throwable $e) {
                $inserted[] = ['error' => $e->getMessage(), 'row' => $flatRow];
            }
        }

        return $inserted;
    }

    private function castValue($value, string $type)
    {
        return match ($type) {
            'integer', 'int' => (int) $value,
            'float', 'double', 'decimal', 'numeric' => (float) $value,
            'boolean', 'bool' => (bool) $value,
            default => (string) $value,
        };
    }

}
