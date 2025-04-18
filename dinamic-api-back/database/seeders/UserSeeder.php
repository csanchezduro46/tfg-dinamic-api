<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Auth\Events\Verified;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'name' => 'Cristina Sánchez',
            'email' => 'csanchezduro46@uoc.edu',
            'password' => Hash::make('SIO0asm102masuoc'),

        ]);

        $user->markEmailAsVerified();
        $user->assignRole('admin');
    }
}
