<?php

namespace Database\Seeders;

use App\Models\ApiCallGroup;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Auth\Events\Verified;
use App\Models\User;

class PlatformGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $groups = [
            'Customers',
            'Products',
            'Orders',
            'Collections',
            'Shipping',
            'Store Properties',
            'Discounts',
            'Online Store',
            'Common Objects'
        ];

        foreach ($groups as $group) {
            ApiCallGroup::firstOrCreate(['name' => $group]);
        }
    }
}
