<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('inventories')->insert([
            'uuid' => Str::uuid()->toString(),
            'buying_price' => 100,
            'quantity' => 20,
            'product_id' => 1,
            'user_id' => 2,
            'measurement_id' => 1,
            'department_id' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        DB::table('inventories')->insert([
            'uuid' => Str::uuid()->toString(),
            'buying_price' => 200,
            'quantity' => 40,
            'product_id' => 2,
            'user_id' => 1,
            'measurement_id' => 1,
            'department_id' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }
}
