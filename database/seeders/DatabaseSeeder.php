<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin selalu dibuat (idempotent: aman dijalankan berulang).
        $this->call(AdminSeeder::class);

        // Data contoh HANYA untuk lingkungan lokal.
        // Jangan sampai data contoh ikut masuk ke server milik UMKM.
        if (app()->environment('local')) {
            $this->call([
                TarifOngkirSeeder::class,
                BahanBakuSeeder::class,
                ProdukSeeder::class,
                PeriodePoSeeder::class,
            ]);
        }
    }
}
