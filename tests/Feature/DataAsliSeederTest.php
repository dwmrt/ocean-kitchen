<?php

namespace Tests\Feature;

use App\Models\BahanBaku;
use App\Models\KomposisiProduk;
use App\Models\Produk;
use App\Models\TarifOngkir;
use Database\Seeders\BahanBakuSeeder;
use Database\Seeders\KomposisiSeeder;
use Database\Seeders\ProdukSeeder;
use Database\Seeders\TarifOngkirSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DataAsliSeederTest extends TestCase
{
    use RefreshDatabase;

    private array $seeders = [
        BahanBakuSeeder::class,
        ProdukSeeder::class,
        KomposisiSeeder::class,
        TarifOngkirSeeder::class,
    ];

    protected function setUp(): void
    {
        parent::setUp();

        // Foto disalin ke disk palsu, bukan ke storage/app/public yang asli.
        Storage::fake('public');

        $this->seed($this->seeders);
    }

    public function test_jumlah_data_sesuai_file_pemilik_toko(): void
    {
        $this->assertSame(16, Produk::count());
        $this->assertSame(41, BahanBaku::count());
        $this->assertSame(140, KomposisiProduk::count());
    }

    public function test_setiap_produk_punya_komposisi_dan_foto(): void
    {
        $this->assertSame(0, Produk::doesntHave('komposisi')->count());

        foreach (Produk::all() as $produk) {
            Storage::disk('public')->assertExists($produk->foto);
        }
    }

    public function test_harga_dan_aturan_order_sesuai_jawaban_pemilik(): void
    {
        $classic = Produk::where('nama_produk', 'Classic brownies')->firstOrFail();
        $this->assertEquals(65000, (float) $classic->harga_jual);

        $donat = Produk::where('nama_produk', 'Donat toping')->firstOrFail();
        $this->assertSame(6, $donat->min_order);
        $this->assertEquals(5000, (float) $donat->harga_jual);

        $bomboloni = Produk::where('nama_produk', 'Bomboloni')->firstOrFail();
        $this->assertSame(3, $bomboloni->min_order);
        $this->assertEquals(4000, (float) $bomboloni->harga_jual);
    }

    public function test_tarif_ongkir_per_kilogram(): void
    {
        $this->assertSame(1, TarifOngkir::count());
        $this->assertEquals(10000, (float) TarifOngkir::first()->biaya);
    }

    public function test_seeder_aman_dijalankan_berulang(): void
    {
        $this->seed($this->seeders);

        $this->assertSame(16, Produk::count());
        $this->assertSame(41, BahanBaku::count());
        $this->assertSame(140, KomposisiProduk::count());
        $this->assertSame(1, TarifOngkir::count());
    }
}
