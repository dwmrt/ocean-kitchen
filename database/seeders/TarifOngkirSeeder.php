<?php

namespace Database\Seeders;

use App\Models\TarifOngkir;
use Illuminate\Database\Seeder;

class TarifOngkirSeeder extends Seeder
{
    // DATA CONTOH. Ganti dengan tarif asli dari Ocean Kitchen.
    public function run(): void
    {
        $tarif = [
            'Zona 1 (dekat)' => 10000,
            'Zona 2 (sedang)' => 15000,
            'Zona 3 (jauh)' => 25000,
        ];

        foreach ($tarif as $wilayah => $biaya) {
            TarifOngkir::updateOrCreate(['wilayah' => $wilayah], ['biaya' => $biaya]);
        }
    }
}
