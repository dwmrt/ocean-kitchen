<?php

namespace Tests\Feature;

use App\Models\Pesanan;
use App\Models\Produk;
use App\Models\TarifOngkir;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BuatPesananContohTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Produk::create([
            'nama_produk' => 'Classic brownies', 'kategori' => 'Brownies', 'harga_jual' => 65000,
            'satuan' => 'Loyang', 'min_order' => 1, 'status' => Produk::STATUS_AKTIF,
        ]);
        TarifOngkir::create(['wilayah' => 'Antar maks. 10 km', 'biaya' => 10000]);
    }

    private function jalankanSebagaiLokal(): void
    {
        $this->app->detectEnvironment(fn () => 'local');
    }

    public function test_menolak_jalan_di_luar_lokal(): void
    {
        $this->artisan('pesanan:contoh')->assertFailed();

        $this->assertSame(0, Pesanan::count());
    }

    public function test_membuat_pesanan_dan_mencetak_kode_yang_bisa_dicek(): void
    {
        $this->jalankanSebagaiLokal();

        $this->artisan('pesanan:contoh')
            ->expectsOutputToContain('KODE PESANAN: OK-')
            ->assertSuccessful();

        $pesanan = Pesanan::firstOrFail();
        $this->withoutVite();

        $this->get('/pesanan/'.$pesanan->kode_pesanan)->assertOk();
    }

    public function test_pilihan_antar_cod_dan_status(): void
    {
        $this->jalankanSebagaiLokal();

        $this->artisan('pesanan:contoh --terima=antar --bayar=cod')->assertSuccessful();
        $p = Pesanan::firstOrFail();
        $this->assertSame('antar', $p->metode_terima);
        $this->assertSame('cod', $p->metode_bayar);
        $this->assertEquals(10000, $p->ongkir);

        $this->artisan('pesanan:contoh --status=ditolak')->assertSuccessful();
        $ditolak = Pesanan::where('status_bayar', Pesanan::BAYAR_DITOLAK)->firstOrFail();
        $this->assertSame('Nominal tidak sesuai', $ditolak->alasan_tolak);

        $this->artisan('pesanan:contoh --status=dibatalkan')->assertSuccessful();
        $this->assertSame(1, Pesanan::where('status_pesanan', Pesanan::PESANAN_DIBATALKAN)->count());
    }

    public function test_pilihan_tidak_valid_ditolak(): void
    {
        $this->jalankanSebagaiLokal();

        $this->artisan('pesanan:contoh --status=aneh')->assertFailed();
        $this->artisan('pesanan:contoh --bayar=kartu')->assertFailed();

        $this->assertSame(0, Pesanan::count());
    }
}
