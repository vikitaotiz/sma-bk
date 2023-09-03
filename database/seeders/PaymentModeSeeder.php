<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PaymentModeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('payment_modes')->insert([
            'uuid' => Str::uuid()->toString(),
            'name' => 'Cash',
            'user_id' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        DB::table('payment_modes')->insert([
            'uuid' => Str::uuid()->toString(),
            'name' => 'Mpesa',
            'user_id' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        DB::table('payment_modes')->insert([
            'uuid' => Str::uuid()->toString(),
            'name' => 'Not Paid',
            'user_id' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        DB::table('payment_modes')->insert([
            'uuid' => Str::uuid()->toString(),
            'name' => 'Mpesa & Cash',
            'user_id' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        DB::table('payment_modes')->insert([
            'uuid' => Str::uuid()->toString(),
            'name' => 'Card',
            'user_id' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        DB::table('payment_modes')->insert([
            'uuid' => Str::uuid()->toString(),
            'name' => 'Debt',
            'user_id' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }
}
