<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        User::updateOrCreate(
            ['email' => 'doublekillringo013@gmail.com'],
            [
                'first_name' => 'User',
                'last_name' => 'Admin',
                'phonenumber' => '012345678',
                'role' => 'admin',
                'password' => Hash::make('123123123'), // change this later
            ]
        );
    }
}
