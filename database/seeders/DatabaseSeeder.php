<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call(DepartmentSeeder::class);
        $this->call(RoleSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(UserDepartmentSeeder::class);
        $this->call(UserRoleSeeder::class);
        $this->call(MeasurementSeeder::class);
        $this->call(PaymentModeSeeder::class);
        // $this->call(CategorySeeder::class);

        // $this->call(ProductSeeder::class);
        // $this->call(InventorySeeder::class);
        // $this->call(BillSeeder::class);
        // $this->call(SaleSeeder::class);
    }
}
