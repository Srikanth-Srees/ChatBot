<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Insert or update users with hashed passwords
        DB::table('users')->updateOrInsert(
            ['email' => 'admin@gmail.com'], // Email as unique identifier
            [
                'name' => 'Admin',
                'password' => Hash::make('admin@123') // Hashing the password
            ]
        );

        DB::table('users')->updateOrInsert(
            ['email' => 'bharadwajkssb@gmail.com'], // Email as unique identifier
            [
                'name' => 'Madhumakshika',
                'password' => Hash::make('Madhumakshika@') // Hashing the password
            ]
        );
    }
}
