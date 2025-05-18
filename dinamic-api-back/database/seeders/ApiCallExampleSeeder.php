<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ApiCall;

class ApiCallExampleSeeder extends Seeder
{
    public function run(): void
    {
        // Ejemplo
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
                    ],
                    "query" => 'mutation customerCreate($input: CustomerInput!) { customerCreate(input: $input) { userErrors { field message } customer { id email phone taxExempt firstName lastName amountSpent { amount currencyCode } smsMarketingConsent { marketingState marketingOptInLevel consentUpdatedAt } } } }'
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
    }
}
