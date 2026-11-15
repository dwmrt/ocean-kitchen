<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Data asli Ocean Kitchen + admin (aman dijalankan berulang: tidak menimpa).
        $this->call([
            AdminSeeder::class,
            BahanBakuSeeder::class,
            ProdukSeeder::class,
            KomposisiSeeder::class,
            TarifOngkirSeeder::class,
        ]);

        // Data contoh HANYA untuk lingkungan lokal (periode PO contoh yang sedang dibuka).
        if (app()->environment('local')) {
            $this->call(PeriodePoSeeder::class);
        }
    }
}
