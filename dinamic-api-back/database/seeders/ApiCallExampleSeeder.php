<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ApiCall;

class ApiCallExampleSeeder extends Seeder
{
    public function run(): void
    {
        // Ejemplo 1
        ApiCall::where('name', 'productCreate')
            ->whereHas('version', fn($q) => $q->where('version', '2024-10'))
            ->update([
                'payload_example' => [
                    'product' => [
                        'title' => 'Producto nuevo',
                        'body_html' => '<strong>Muy bueno</strong>',
                        'vendor' => 'Vendedor 1',
                        'variants' => [['option1' => 'Azul', 'price' => '19.95']]
                    ]
                ],
                'response_example' => [
                    'product' => [
                        'id' => 123456789,
                        'title' => 'New Product',
                        'variants' => [['id' => 123456, 'price' => '19.95']]
                    ]
                ]
            ]);

        // Ejemplo 2
        ApiCall::where('name', 'customerCreate')
            ->whereHas('version', fn($q) => $q->where('version', '2024-10'))
            ->update([
                'payload_example' => [
                    'customer' => [
                        'first_name' => 'Cristina',
                        'last_name' => 'Sánchez',
                        'email' => 'csanchezduro46@uoc.edu',
                        'verified_email' => true,
                        'addresses' => [['address1' => 'Calle Prueba, 1', 'city' => 'Logroño', 'country' => 'España']]
                    ]
                ],
                'response_example' => [
                    'customer' => [
                        'id' => 12345,
                        'email' => 'csanchezduro46@uoc.edu',
                        'first_name' => 'Cristina',
                        'last_name' => 'Sánchez',
                        'orders_count' => 0
                    ]
                ]
            ]);

        // Ejemplo 3
        ApiCall::where('name', 'orderCreate')
            ->whereHas('version', fn($q) => $q->where('version', '2024-10'))
            ->update([
                'payload_example' => [
                    'order' => [
                        'line_items' => [['variant_id' => 123456, 'quantity' => 1]],
                        'customer' => ['id' => 12345],
                        'financial_status' => 'paid'
                    ]
                ],
                'response_example' => [
                    'order' => [
                        'id' => 1234,
                        'total_price' => '19.95',
                        'currency' => 'EUR',
                        'financial_status' => 'paid',
                        'customer' => [
                            'id' => 12345,
                            'email' => 'csanchezduro46@uoc.edu'
                        ]
                    ]
                ]
            ]);
    }
}
