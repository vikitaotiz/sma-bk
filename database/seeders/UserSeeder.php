<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'uuid' => Str::uuid()->toString(),
            'name' => 'Administrator',
            'active' => true,
            'email' => 'admin@email.com',
            'password' => Hash::make('admin@2023'),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        DB::table('users')->insert([
            'uuid' => Str::uuid()->toString(),
            'name' => 'Butcher',
            'active' => true,
            'email' => 'butcher@email.com',
            'password' => Hash::make('butcher@2024'),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        DB::table('users')->insert([
            'uuid' => Str::uuid()->toString(),
            'name' => 'General User',
            'active' => true,
            'email' => 'general@email.com',
            'password' => Hash::make('general@2025'),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        DB::table('users')->insert([
            'uuid' => Str::uuid()->toString(),
            'name' => 'Forbidden User',
            'active' => false,
            'email' => 'forbidden@email.com',
            'password' => Hash::make('forbidden@2023'),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }
}
