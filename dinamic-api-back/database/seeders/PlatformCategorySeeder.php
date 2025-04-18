<?php

namespace Database\Seeders;

use App\Models\ApiCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Auth\Events\Verified;
use App\Models\User;

class PlatformCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
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

        foreach ($categories as $category) {
            ApiCategory::firstOrCreate(['name' => $category]);
        }
    }
}
