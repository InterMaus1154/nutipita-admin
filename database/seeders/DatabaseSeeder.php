<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Product;
use App\Models\User;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    private function createData()
    {
        Product::create([
            'product_name' => 'White normal',
            'product_weight_g' => 100,
            'product_qty_per_pack' => 5
        ]);

        Product::create([
            'product_name' => 'White medium',
            'product_weight_g' => 60,
            'product_qty_per_pack' => 15
        ]);

        Product::create([
            'product_name' => 'Brown normal',
            'product_weight_g' => 100,
            'product_qty_per_pack' => 5
        ]);

        Product::create([
            'product_name' => 'Brown medium',
            'product_weight_g' => 60,
            'product_qty_per_pack' => 15
        ]);

//        Customer::factory(2)->create();
    }

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (!app()->isProduction()) {
            User::create([
                'username' => 'test1',
                'password' => 'test'
            ]);

            $this->createData();

        }
    }
}
