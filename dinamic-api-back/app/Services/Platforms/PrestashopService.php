<?php

namespace App\Services\Platforms;

use Crypt;
use Illuminate\Support\Facades\Http;

class PrestashopService
{
    public function testConnection(array $credentials, string $storeUrl, string $version = null): bool
    {
        if (!isset($credentials['api_key'])) {
            return false;
        }

        // Construimos la URL básica
        $url = rtrim($storeUrl, '/') . '/api/customers?output_format=JSON';

        try {
            $response = Http::withBasicAuth($credentials['api_key'], '')
                ->timeout(10)
                ->get($url);

            return $response->successful();
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function sendApiCall(ApiCall $apiCall, ?PlatformConnection $connection, array $payload)
    {
        if (!$connection) {
            throw new \Exception("No se ha proporcionado la conexión de PrestaShop.");
        }

        $apiKey = null;

        foreach ($connection->credentials as $cred) {
            if (strtolower($cred->necessaryKey->key) === 'api_key') {
                $apiKey = Crypt::decryptString($cred->value);
                break;
            }
        }

        if (!$apiKey) {
            throw new \Exception("La clave 'api_key' es obligatoria para PrestaShop.");
        }

        $url = rtrim($connection->store_url, '/') . $apiCall->endpoint;

        $method = strtolower($apiCall->method ?? 'get');

        $http = Http::withBasicAuth($apiKey, '')
            ->withHeaders(['Content-Type' => 'application/json']);

        $response = match ($method) {
            'get' => $http->get($url, $payload),
            'post' => $http->post($url, $payload),
            'put' => $http->put($url, $payload),
            'delete' => $http->delete($url, $payload),
            default => throw new \Exception("Método HTTP no soportado: $method"),
        };

        if (!$response->successful()) {
            throw new \Exception("Error en la llamada a la API de PrestaShop: " . $response->body());
        }

        return $response;
    }
}