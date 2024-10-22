<?php
// database/seeders/UsersTableSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('users')->insert([
            [
                'id' => 1,
                'Fname' => 'System',
                'Lname' => '',
                'email' => '',
                'tell' => '',
                'password' => '',
                'password_Org' => '',
                'role' => 1,
                'imgUrl' => 'img/users/user.png',
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 2,
                'Fname' => 'Annour',
                'Lname' => 'technology',
                'email' => 'admin@gmail.com',
                'tell' => '054356543212',
                'password' => Hash::make('annourtechnologie'), // Adjust password hashing as needed
                'password_Org' => '$2a$12$WLiIZZRU9f0uKLEEf7nmFuOZAEpzXeycwbsIGydIzMLqOiZtIVdKy',
                'role' => 1,
                'imgUrl' => 'img/users/user.png',
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }
}
