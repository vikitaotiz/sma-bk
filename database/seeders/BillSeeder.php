<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('bills')->insert([
            'uuid' => Str::uuid()->toString(),
            'status' => 'sold',
            'selling_price' => 100,
            'payment_mode_id' => 1,
            'user_id' => 1,
            'department_id' => 2,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        DB::table('bills')->insert([
            'uuid' => Str::uuid()->toString(),
            'status' => 'pending',
            'selling_price' => 0,
            'payment_mode_id' => 3,
            'user_id' => 1,
            'department_id' => 3,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }
}
