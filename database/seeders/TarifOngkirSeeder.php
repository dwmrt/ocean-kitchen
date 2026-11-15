<?php

namespace Database\Seeders;

use App\Models\TarifOngkir;
use Illuminate\Database\Seeder;

class TarifOngkirSeeder extends Seeder
{
    /**
     * Tarif asli dari pemilik toko: Rp 10.000 per kilogram.
     * Kolom "biaya" berarti BIAYA PER KG.
     * Ongkir pesanan = pembulatan ke atas (total berat dalam kg) x biaya.
     * Rumus dan pembulatan masih menunggu konfirmasi pemilik toko.
     */
    public function run(): void
    {
        TarifOngkir::firstOrCreate(
            ['wilayah' => 'Semua wilayah'],
            ['biaya' => 10000]
        );
    }
}
