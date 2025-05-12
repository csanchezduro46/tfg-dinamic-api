<?php

namespace App\Services\Platforms;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;
use App\Models\ApiCall;
use App\Models\PlatformConnection;

class ShopifyService
{
    public function testConnection(array $credentials, string $storeUrl, string $version): bool
    {
        if (!isset($credentials['access_token'])) {
            return false;
        }
        $accessToken = Crypt::decryptString($credentials['access_token']);

        $url = "https://{$storeUrl}.myshopify.com/admin/api/{$version}/graphql.json";

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-Shopify-Access-Token' => $accessToken,
            ])->post($url, [
                        'query' => '{ products(first: 3) { edges { node { id title } } } }'
                    ]);

            return $response->successful();
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function sendApiCall(ApiCall $apiCall, ?PlatformConnection $connection, array $payload)
    {
        if (!$connection) {
            throw new \Exception("No se ha proporcionado la conexión de Shopify.");
        }

        $accessToken = null;

        foreach ($connection->credentials as $cred) {
            if (strtolower($cred->necessaryKey->key) === 'access_token') {
                $accessToken = Crypt::decryptString($cred->value);
                break;
            }
        }


        if (!$accessToken) {
            throw new \Exception("La clave 'access_token' es obligatoria para Shopify.");
        }

        $headers = [
            'Content-Type' => 'application/json',
            'X-Shopify-Access-Token' => $accessToken
        ];

        $storeUrl = $connection->store_url;

        $url = "https://{$storeUrl}.myshopify.com{$apiCall->endpoint}";

        $shopifyPayload = $this->buildGraphQLPayload($apiCall, $payload);

        // Las llamadsa de Shopify tienen 'query' y 'variables'
        if (!isset($shopifyPayload['query']) || !isset($shopifyPayload['variables'])) {
            throw new \Exception("El payload de Shopify GraphQL debe contener 'query' y 'variables'.");
        }

        $response = Http::withHeaders($headers)
            ->post($url, $shopifyPayload);
        $json = $response->json();

        if (isset($json['errors'])) {
            throw new \Exception('Shopify GraphQL Error: ' . json_encode($json['errors']));
        }

        if (isset($json['data'][$apiCall->name]['userErrors']) && count($json['data'][$apiCall->name]['userErrors']) > 0) {
            $msg = collect($json['data'][$apiCall->name]['userErrors'])
                ->pluck('message')
                ->implode('; ');
            throw new \Exception("Shopify Validation: " . $msg);
        }

        return $response;
    }

    private function buildGraphQLPayload(ApiCall $apiCall, array $inputData)
    {
        $query = $apiCall->payload_example['query'] ?? null;

        if (!$query) {
            throw new \Exception("No se ha definido una query GraphQL en el ApiCall.");
        }

        return [
            'query' => $query,
            'variables' => [
                'input' => $inputData
            ]
        ];
    }


}