<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            CabangSeeder::class,
            ProdukSeeder::class,
            UserSeeder::class,
        ]);
    }
}