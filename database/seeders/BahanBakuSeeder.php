<?php

namespace Database\Seeders;

use App\Models\BahanBaku;
use Illuminate\Database\Seeder;

class BahanBakuSeeder extends Seeder
{
    /**
     * Data asli dari database/seeders/data/bahan_baku.php.
     * Memakai firstOrCreate: data yang sudah ada TIDAK ditimpa, jadi perubahan
     * harga yang dilakukan admin lewat aplikasi tidak hilang kalau seeder diulang.
     * Untuk memuat ulang data dari awal di lokal: php artisan migrate:fresh --seed
     */
    public function run(): void
    {
        foreach (require __DIR__.'/data/bahan_baku.php' as $nama => [$satuan, $jumlah, $harga, $estimasi]) {
            BahanBaku::firstOrCreate(
                ['nama_bahan' => $nama],
                [
                    'satuan' => $satuan,
                    'jumlah' => $jumlah,
                    'harga_bahan' => $harga,
                    'harga_estimasi' => $estimasi,
                ]
            );
        }
    }
}
