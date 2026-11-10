<?php

namespace Database\Seeders;

use App\Models\BahanBaku;
use App\Models\KomposisiProduk;
use App\Models\Produk;
use Illuminate\Database\Seeder;

class ProdukSeeder extends Seeder
{
    // DATA CONTOH. Ganti dengan produk, harga, dan resep asli Ocean Kitchen.
    public function run(): void
    {
        $produk = [
            ['Brownies Cokelat', 'Brownies', 45000, 'aktif'],
            ['Brownies Keju', 'Brownies', 48000, 'aktif'],
            ['Choco Chip Cookies', 'Cookies', 35000, 'aktif'],
            ['Donat Gula Halus', 'Donuts', 30000, 'aktif'],
            ['Pie Susu', 'Pie', 25000, 'aktif'],
            ['Bolu Pandan', 'Cakes', 55000, 'aktif'],
            // Untuk menguji bahwa produk nonaktif tidak tampil di katalog.
            ['Menu Lama (nonaktif)', 'Cakes', 40000, 'nonaktif'],
        ];

        foreach ($produk as [$nama, $kategori, $harga, $status]) {
            Produk::updateOrCreate(
                ['nama_produk' => $nama],
                [
                    'kategori' => $kategori,
                    'deskripsi' => '[CONTOH] Data contoh untuk pengembangan.',
                    'harga_jual' => $harga,
                    'status' => $status,
                    // hpp sengaja kosong: diisi oleh HitungHPP() nanti.
                ]
            );
        }

        // Komposisi hanya untuk 2 produk. Produk lain sengaja tanpa komposisi
        // supaya kasus "HPP belum bisa dihitung" bisa diuji.
        $this->komposisi('Brownies Cokelat', [
            ['Tepung terigu', 100],
            ['Gula pasir', 120],
            ['Mentega', 100],
            ['Telur', 2],
            ['Cokelat batang', 150],
        ]);

        $this->komposisi('Choco Chip Cookies', [
            ['Tepung terigu', 150],
            ['Gula pasir', 100],
            ['Mentega', 100],
            ['Telur', 1],
            ['Cokelat batang', 100],
        ]);
    }

    private function komposisi(string $namaProduk, array $daftar): void
    {
        $idProduk = Produk::where('nama_produk', $namaProduk)->value('id_produk');

        foreach ($daftar as [$namaBahan, $jumlahPakai]) {
            $idBahan = BahanBaku::where('nama_bahan', $namaBahan)->value('id_bahan');

            KomposisiProduk::updateOrCreate(
                ['id_produk' => $idProduk, 'id_bahan' => $idBahan],
                ['jumlah_pakai' => $jumlahPakai]
            );
        }
    }
}
