<?php

namespace Database\Seeders;

use App\Models\BahanBaku;
use App\Models\KomposisiProduk;
use App\Models\Produk;
use Illuminate\Database\Seeder;

class KomposisiSeeder extends Seeder
{
    /**
     * Harus dijalankan setelah BahanBakuSeeder dan ProdukSeeder.
     */
    public function run(): void
    {
        $produk = Produk::pluck('id_produk', 'nama_produk');
        $bahan = BahanBaku::pluck('id_bahan', 'nama_bahan');

        foreach (require __DIR__.'/data/komposisi.php' as [$namaProduk, $namaBahan, $jumlahPakai]) {
            if (! isset($produk[$namaProduk]) || ! isset($bahan[$namaBahan])) {
                throw new \RuntimeException("Komposisi tidak cocok: {$namaProduk} / {$namaBahan}");
            }

            KomposisiProduk::firstOrCreate(
                ['id_produk' => $produk[$namaProduk], 'id_bahan' => $bahan[$namaBahan]],
                ['jumlah_pakai' => $jumlahPakai]
            );
        }
    }
}
