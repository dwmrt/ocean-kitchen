<?php

namespace Database\Seeders;

use App\Models\TarifOngkir;
use App\Services\OngkirService;
use Illuminate\Database\Seeder;

class TarifOngkirSeeder extends Seeder
{
    /**
     * Aturan dari pemilik toko: pengantaran (ojol atau diantar pemilik) MAKSIMAL 10 km.
     * Di dalam jarak itu ongkir Rp 10.000 (satu blok 10 km, lihat OngkirService).
     * Di luar 10 km tidak diantar lewat sistem ini (dikirim lewat ekspedisi, diurus
     * pemilik secara manual). Ambil di rumah gratis (id_tarif kosong di pesanan).
     *
     * Kalau batas jangkauan nanti dinaikkan, tambahkan tingkat baru di sini atau lewat admin;
     * rumus untuk jarak lebih jauh sudah ada di OngkirService.
     */
    public function run(): void
    {
        TarifOngkir::firstOrCreate(
            ['wilayah' => 'Antar maks. 10 km'],
            ['biaya' => (new OngkirService())->hitung(10)]
        );
    }
}
