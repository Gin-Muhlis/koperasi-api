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
        $this->call(UserSeeder::class);
        $this->call(RoleSeeder::class);
        $this->call(MemberSeeder::class);
        $this->call(CategorySeeder::class);
        $this->call(SubCategorySeeder::class);
        $this->call(SavingSeeder::class);
        $this->call(productSeeder::class);
        $this->call(StuffSeeder::class);
        $this->call(PurchaseSeeder::class);
        $this->call(SaleSeeder::class);
        $this->call(SalesDetailSeeder::class);
        $this->call(LoanSeeder::class);
        $this->call(InstallmentSeeder::class);
        $this->call(PaymentDeteminationsSeeder::class);
    }
}
