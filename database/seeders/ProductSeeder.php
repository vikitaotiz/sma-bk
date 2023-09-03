<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('products')->insert([
            'uuid' => Str::uuid()->toString(),
            'name' => 'Beaf',
            'quantity' => 50,
            'buying_price' => 120,
            'selling_price' => 160,
            'user_id' => 2,
            'category_id' => 1,
            'measurement_id' => 1,
            'department_id' => 3,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        DB::table('products')->insert([
            'uuid' => Str::uuid()->toString(),
            'name' => 'Tomatoes',
            'quantity' => 40,
            'buying_price' => 20,
            'selling_price' => 50,
            'user_id' => 1,
            'category_id' => 2,
            'measurement_id' => 3,
            'department_id' => 2,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        DB::table('products')->insert([
            'uuid' => Str::uuid()->toString(),
            'name' => 'Milk',
            'quantity' => 80,
            'buying_price' => 20,
            'selling_price' => 50,
            'user_id' => 1,
            'category_id' => 2,
            'measurement_id' => 2,
            'department_id' => 2,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }
}
