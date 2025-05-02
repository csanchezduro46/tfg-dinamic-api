<?php

namespace App\Services\Platforms;

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
}
