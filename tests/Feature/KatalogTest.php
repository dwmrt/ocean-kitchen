<?php

namespace Tests\Feature;

use App\Models\PeriodePo;
use App\Models\Produk;
use Database\Seeders\BahanBakuSeeder;
use Database\Seeders\KomposisiSeeder;
use Database\Seeders\ProdukSeeder;
use Database\Seeders\TarifOngkirSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class KatalogTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        Storage::fake('public');

        $this->seed([
            BahanBakuSeeder::class,
            ProdukSeeder::class,
            KomposisiSeeder::class,
            TarifOngkirSeeder::class,
        ]);
    }

    public function test_katalog_menampilkan_semua_produk_aktif(): void
    {
        $this->get('/katalog')
            ->assertOk()
            ->assertViewHas('produk', fn ($produk) => $produk->count() === 16);
    }

    public function test_produk_nonaktif_tidak_tampil_dan_detailnya_404(): void
    {
        $produk = Produk::where('nama_produk', 'Classic brownies')->firstOrFail();
        $produk->update(['status' => Produk::STATUS_NONAKTIF]);

        $this->get('/katalog')
            ->assertViewHas('produk', fn ($p) => $p->count() === 15);

        $this->get('/produk/'.$produk->id_produk)->assertNotFound();
    }

    public function test_filter_kategori(): void
    {
        $jumlah = Produk::where('kategori', 'Pie')->count();
        $this->assertGreaterThan(0, $jumlah);

        $this->get('/katalog?kategori=Pie')
            ->assertOk()
            ->assertViewHas('produk', fn ($p) => $p->count() === $jumlah
                && $p->every(fn ($x) => $x['kategori'] === 'Pie'));
    }

    public function test_kategori_tidak_dikenal_menjadi_semua(): void
    {
        $this->get('/katalog?kategori='.urlencode('Tidak ada'))
            ->assertViewHas('kategori', 'Semua');
    }

    public function test_daftar_kategori_dimulai_dari_semua(): void
    {
        $this->get('/katalog')
            ->assertViewHas('kategoriList', fn ($list) => $list[0] === 'Semua'
                && in_array('Donat', $list, true)
                && in_array('Minuman', $list, true));
    }

    public function test_detail_produk(): void
    {
        $produk = Produk::where('nama_produk', 'Donat toping')->firstOrFail();

        $this->get('/produk/'.$produk->id_produk)
            ->assertOk()
            ->assertViewIs('produk.detail')
            ->assertSee('Donat toping');
    }

    public function test_detail_produk_tidak_ada_mengembalikan_404(): void
    {
        $this->get('/produk/99999')->assertNotFound();
    }

    public function test_banner_po_tanpa_periode_tidak_error(): void
    {
        $this->get('/katalog')
            ->assertOk()
            ->assertViewHas('po', fn ($po) => $po['dibuka'] === false);
    }

    public function test_banner_po_dibuka_sesuai_tanggal(): void
    {
        PeriodePo::create([
            'nama_periode' => 'PO Tes',
            'tanggal_buka' => today(),
            'tanggal_tutup' => today()->addDays(3),
            'tanggal_terima' => today()->addDays(5),
            'status' => PeriodePo::STATUS_AKTIF,
        ]);

        $this->get('/katalog')
            ->assertViewHas('po', fn ($po) => $po['dibuka'] === true && $po['nama'] === 'PO Tes');
    }

    public function test_periode_yang_sudah_tutup_tidak_dianggap_dibuka(): void
    {
        PeriodePo::create([
            'nama_periode' => 'PO Lama',
            'tanggal_buka' => today()->subDays(10),
            'tanggal_tutup' => today()->subDay(),
            'tanggal_terima' => today()->addDays(2),
            'status' => PeriodePo::STATUS_AKTIF,
        ]);

        $this->get('/katalog')
            ->assertViewHas('po', fn ($po) => $po['dibuka'] === false);
    }

    public function test_ongkir_per_tingkat_jarak(): void
    {
        $this->get('/katalog')
            ->assertViewHas('ongkir', fn ($o) => count($o) === 3
                && $o[0]['wilayah'] === 'Jarak 0-14 km'
                && $o[0]['biaya'] === 10000
                && $o[1]['biaya'] === 20000
                && $o[2]['biaya'] === 30000);
    }
}
