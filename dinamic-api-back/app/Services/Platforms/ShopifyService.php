<?php

namespace App\Services\Platforms;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;

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
}