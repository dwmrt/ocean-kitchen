<?php

namespace Database\Seeders;

use App\Models\BahanBaku;
use Illuminate\Database\Seeder;

class BahanBakuSeeder extends Seeder
{
    // DATA CONTOH. harga_bahan = harga untuk sejumlah "jumlah" satuan.
    // Contoh: tepung 1000 gram seharga 14000 => Rp 14 per gram.
    public function run(): void
    {
        $bahan = [
            ['Tepung terigu', 'gram', 1000, 14000],
            ['Gula pasir', 'gram', 1000, 17000],
            ['Mentega', 'gram', 200, 18000],
            ['Telur', 'butir', 10, 28000],
            ['Cokelat batang', 'gram', 500, 45000],
            ['Susu cair', 'ml', 1000, 18000],
        ];

        foreach ($bahan as [$nama, $satuan, $jumlah, $harga]) {
            BahanBaku::updateOrCreate(
                ['nama_bahan' => $nama],
                ['satuan' => $satuan, 'jumlah' => $jumlah, 'harga_bahan' => $harga]
            );
        }
    }
}
