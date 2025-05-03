<?php

namespace Database\Seeders;

use App\Models\Platform;
use Illuminate\Database\Seeder;

class PlatformSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->createShopifyVersions();
        $this->createPrestashopVersions();

    }

    public function createShopifyVersions(): void
    {
        $shopify = Platform::create([
            'name' => 'Shopify',
            'slug' => 'shopify',
        ]);

        $shopify->versions()->createMany([
            [
                'version' => '2024-10',
                'description' => 'Versión de octubre de 2024 de la API de Shopify'
            ],
            [
                'version' => '2025-01',
                'description' => 'Versión de enero de 2025 con mejoras en la API de Shopify'
            ],
            [
                'version' => '2025-04',
                'description' => 'Versión de abril de 2025 con mejoras en la API de Shopify'
            ]
        ]);
    }

    public function createPrestashopVersions(): void
    {
        $presta = Platform::create([
            'name' => 'PrestaShop',
            'slug' => 'prestashop',
        ]);

        $presta->versions()->createMany([
            [
                'version' => '1.7',
                'description' => 'Versión clásica con API XML'
            ],
            [
                'version' => '8',
                'description' => 'Versión mejorada con JSON'
            ],
            [
                'version' => '9',
                'description' => 'Versión con API Platform (JSON)'
            ]
        ]);
    }
}
