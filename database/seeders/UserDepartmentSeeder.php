<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UserDepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin Department
        DB::table('department_user')->insert([
            'user_id' => 1,
            'department_id' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        DB::table('department_user')->insert([
            'user_id' => 1,
            'department_id' => 2,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        DB::table('department_user')->insert([
            'user_id' => 1,
            'department_id' => 3,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        DB::table('department_user')->insert([
            'user_id' => 1,
            'department_id' => 4,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        // Butcher Department
        DB::table('department_user')->insert([
            'user_id' => 2,
            'department_id' => 2,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        // General User Department
        DB::table('department_user')->insert([
            'user_id' => 3,
            'department_id' => 3,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);


        // DB::table('department_user')->insert([
        //     'user_id' => 6,
        //     'department_id' => 4,
        //     'created_at' => Carbon::now(),
        //     'updated_at' => Carbon::now()
        // ]);

        // DB::table('department_user')->insert([
        //     'user_id' => 7,
        //     'department_id' => 4,
        //     'created_at' => Carbon::now(),
        //     'updated_at' => Carbon::now()
        // ]);

        // DB::table('department_user')->insert([
        //     'user_id' => 9,
        //     'department_id' => 4,
        //     'created_at' => Carbon::now(),
        //     'updated_at' => Carbon::now()
        // ]);
    }
}
