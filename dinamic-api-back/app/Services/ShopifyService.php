<?php

namespace App\Services\Platforms;

use Illuminate\Support\Facades\Http;

class ShopifyService
{
    public function testConnection(array $credentials, string $storeUrl, string $version): bool
    {
        if (!isset($credentials['access_token'])) {
            return false;
        }

        $url = "https://{$storeUrl}/admin/api/{$version}/shop.json";

        try {
            $response = Http::withHeaders([
                'X-Shopify-Access-Token' => $credentials['access_token'],
            ])->get($url);

            return $response->successful();
        } catch (\Throwable $e) {
            return false;
        }
    }
}
