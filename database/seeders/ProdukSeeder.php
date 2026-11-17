<?php

namespace Database\Seeders;

use App\Models\Produk;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class ProdukSeeder extends Seeder
{
    /**
     * Data asli dari database/seeders/data/produk.php (16 produk Ocean Kitchen).
     *
     * - firstOrCreate: produk yang sudah ada (misalnya sudah diubah admin) TIDAK ditimpa.
     * - Foto disalin dari database/seeders/foto ke storage/app/public/produk
     *   (hanya kalau belum ada di sana). Tampilkan dengan: asset('storage/'.$produk->foto)
     *   setelah menjalankan: php artisan storage:link
     * - hpp sengaja kosong: diisi oleh HitungHPP() nanti.
     */
    public function run(): void
    {
        foreach (require __DIR__.'/data/produk.php' as $data) {
            Produk::firstOrCreate(['nama_produk' => $data['nama_produk']], $data);

            $this->salinFoto($data['foto']);
        }
    }

    private function salinFoto(?string $foto): void
    {
        if (! $foto) {
            return;
        }

        $sumber = __DIR__.'/foto/'.basename($foto);

        if (is_file($sumber) && ! Storage::disk('public')->exists($foto)) {
            Storage::disk('public')->put($foto, file_get_contents($sumber));
        }
    }
}
