<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('sales')->insert([
            'uuid' => Str::uuid()->toString(),
            'name' => 'Milk',
            'quantity' => 5,
            'user_id' => 3,
            'bill_id' => 1,
            'status' => 'sold',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        DB::table('sales')->insert([
            'uuid' => Str::uuid()->toString(),
            'name' => 'Beef',
            'quantity' => 10,
            'user_id' => 2,
            'bill_id' => 2,
            'status' => 'sold',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }
}
