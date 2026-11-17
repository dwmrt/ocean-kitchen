<?php

namespace Database\Seeders;

use App\Models\TarifOngkir;
use App\Services\OngkirService;
use Illuminate\Database\Seeder;

class TarifOngkirSeeder extends Seeder
{
    /**
     * Tingkat jarak untuk pilihan manual pelanggan. Biaya dihitung dari aturan
     * penjual di OngkirService (satu-satunya sumber rumus):
     *   Rp 10.000 per 10 km, dibulatkan ke blok terdekat.
     *   0-14 km = 10.000 | 15-24 km = 20.000 | 25-34 km = 30.000
     * Ambil di rumah tidak dikenai ongkir (id_tarif kosong di pesanan).
     * Batas jangkauan antar bisa ditambah atau dikurangi admin.
     */
    public function run(): void
    {
        $ongkir = new OngkirService();

        // [label, contoh jarak di dalam tingkat itu]
        foreach ([['0-14', 10], ['15-24', 20], ['25-34', 30]] as [$label, $contohKm]) {
            TarifOngkir::firstOrCreate(
                ['wilayah' => "Jarak {$label} km"],
                ['biaya' => $ongkir->hitung($contohKm)]
            );
        }
    }
}
