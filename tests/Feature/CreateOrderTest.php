<?php

namespace Tests\Feature;

use App\Models\DetailPesanan;
use App\Models\PeriodePo;
use App\Models\Pesanan;
use App\Models\Produk;
use App\Models\TarifOngkir;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateOrderTest extends TestCase
{
    use RefreshDatabase;

    private Produk $brownies;
    private Produk $donat;
    private TarifOngkir $tarif;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->travelTo(Carbon::parse('2026-11-18 10:00:00'));

        PeriodePo::create([
            'nama_periode' => 'PO Tes',
            'tanggal_buka' => '2026-11-17',
            'tanggal_tutup' => '2026-11-22',
            'tanggal_terima' => '2026-11-27',
            'status' => PeriodePo::STATUS_AKTIF,
        ]);

        $this->brownies = Produk::create([
            'nama_produk' => 'Classic brownies', 'kategori' => 'Brownies', 'harga_jual' => 65000,
            'satuan' => 'Loyang', 'min_order' => 1, 'status' => Produk::STATUS_AKTIF,
        ]);

        $this->donat = Produk::create([
            'nama_produk' => 'Donat toping', 'kategori' => 'Donat', 'harga_jual' => 5000,
            'satuan' => 'Pcs', 'min_order' => 6, 'status' => Produk::STATUS_AKTIF,
        ]);

        $this->tarif = TarifOngkir::create(['wilayah' => 'Jarak 15-24 km', 'biaya' => 20000]);
    }

    private function data(array $ubah = []): array
    {
        return array_merge([
            'nama_pelanggan' => 'Sinta Dewi',
            'no_hp' => '081234567890',
            'metode_terima' => 'ambil',
            'jadwal_terima' => '2026-11-27 14:00',
            'metode_bayar' => 'non_tunai',
            'items' => [['id_produk' => $this->brownies->id_produk, 'jumlah' => 2]],
        ], $ubah);
    }

    public function test_pesanan_ambil_non_tunai_tersimpan_lengkap(): void
    {
        $this->postJson('/pesan', $this->data())
            ->assertCreated()
            ->assertJsonPath('total_bayar', 130000)
            ->assertJsonPath('metode_bayar', 'non_tunai')
            ->assertJsonPath('status_bayar', 'belum_dibayar')
            ->assertJsonStructure(['kode_pesanan', 'batas_bayar', 'url_bayar']);

        $p = Pesanan::with('detail')->firstOrFail();

        $this->assertMatchesRegularExpression('/^OK-[A-HJ-NP-Z2-9]{8}$/', $p->kode_pesanan);
        $this->assertSame('baru', $p->status_pesanan);
        $this->assertSame('belum_dibayar', $p->status_bayar);
        $this->assertSame('2026-11-19 10:00:00', $p->batas_bayar->toDateTimeString());
        $this->assertNull($p->alamat);
        $this->assertNull($p->id_tarif);
        $this->assertEquals(0, $p->ongkir);
        $this->assertEquals(130000, $p->total_bayar);

        $this->assertCount(1, $p->detail);
        $this->assertSame(2, $p->detail[0]->jumlah);
        $this->assertEquals(65000, $p->detail[0]->harga_satuan);
        $this->assertEquals(130000, $p->detail[0]->subtotal);
    }

    public function test_pesanan_antar_cod_menyertakan_ongkir_tanpa_batas_bayar(): void
    {
        $this->postJson('/pesan', $this->data([
            'metode_terima' => 'antar',
            'alamat' => 'Jl. Melati No. 5, RT 02 RW 01, dekat masjid',
            'id_tarif' => $this->tarif->id_tarif,
            'metode_bayar' => 'cod',
        ]))
            ->assertCreated()
            ->assertJsonPath('total_bayar', 150000)
            ->assertJsonPath('batas_bayar', null);

        $p = Pesanan::firstOrFail();

        $this->assertSame('cod', $p->metode_bayar);
        $this->assertNull($p->batas_bayar);
        $this->assertEquals(20000, $p->ongkir);
        $this->assertSame($this->tarif->id_tarif, $p->id_tarif);
        $this->assertSame('Jl. Melati No. 5, RT 02 RW 01, dekat masjid', $p->alamat);
    }

    public function test_total_dan_ongkir_dari_browser_diabaikan(): void
    {
        $this->postJson('/pesan', $this->data([
            'metode_terima' => 'antar',
            'alamat' => 'Jl. Melati No. 5, RT 02 RW 01, dekat masjid',
            'id_tarif' => $this->tarif->id_tarif,
            'ongkir' => 0,           // upaya mengakali
            'total_bayar' => 1,      // upaya mengakali
        ]))->assertCreated();

        $p = Pesanan::firstOrFail();

        $this->assertEquals(20000, $p->ongkir);
        $this->assertEquals(150000, $p->total_bayar);
    }

    public function test_ambil_mengabaikan_alamat_dan_tarif(): void
    {
        $this->postJson('/pesan', $this->data([
            'alamat' => 'Alamat yang seharusnya diabaikan karena ambil',
            'id_tarif' => $this->tarif->id_tarif,
        ]))->assertCreated();

        $p = Pesanan::firstOrFail();

        $this->assertNull($p->alamat);
        $this->assertNull($p->id_tarif);
        $this->assertEquals(0, $p->ongkir);
    }

    public function test_antar_wajib_alamat_dan_tingkat_jarak(): void
    {
        $this->postJson('/pesan', $this->data(['metode_terima' => 'antar']))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['alamat', 'id_tarif']);

        $this->assertSame(0, Pesanan::count());
    }

    public function test_ditolak_kalau_po_sedang_tutup(): void
    {
        $this->travelTo(Carbon::parse('2026-11-25 10:00:00'));

        $this->postJson('/pesan', $this->data())
            ->assertStatus(422)
            ->assertJsonValidationErrors('periode');

        $this->assertSame(0, Pesanan::count());
    }

    public function test_jadwal_harus_pada_tanggal_terima_periode(): void
    {
        $this->postJson('/pesan', $this->data(['jadwal_terima' => '2026-11-28 14:00']))
            ->assertStatus(422)
            ->assertJsonValidationErrors('jadwal_terima');

        $this->postJson('/pesan', $this->data(['jadwal_terima' => '2026-11-27T09:30']))
            ->assertCreated();
    }

    public function test_nomor_hp_dirapikan_dan_divalidasi(): void
    {
        $this->postJson('/pesan', $this->data(['no_hp' => '+62 812-3456-7890']))->assertCreated();
        $this->assertSame('081234567890', Pesanan::firstOrFail()->no_hp);

        $this->postJson('/pesan', $this->data(['no_hp' => 'abc123']))
            ->assertStatus(422)
            ->assertJsonValidationErrors('no_hp');
    }

    public function test_metode_bayar_tidak_dikenal_ditolak(): void
    {
        $this->postJson('/pesan', $this->data(['metode_bayar' => 'kartu']))
            ->assertStatus(422)
            ->assertJsonValidationErrors('metode_bayar');
    }

    public function test_minimal_order_ditolak_dan_tidak_ada_yang_tersimpan(): void
    {
        $this->postJson('/pesan', $this->data([
            'items' => [['id_produk' => $this->donat->id_produk, 'jumlah' => 5]],
        ]))
            ->assertStatus(422)
            ->assertJsonValidationErrors('items');

        $this->assertSame(0, Pesanan::count());
        $this->assertSame(0, DetailPesanan::count());
    }

    public function test_harga_di_pesanan_lama_tidak_berubah_saat_harga_produk_naik(): void
    {
        $this->postJson('/pesan', $this->data())->assertCreated();

        $this->brownies->update(['harga_jual' => 99000]);

        $this->assertEquals(65000, DetailPesanan::firstOrFail()->harga_satuan);
        $this->assertEquals(130000, Pesanan::firstOrFail()->total_bayar);
    }

    public function test_kode_pesanan_unik(): void
    {
        $a = $this->postJson('/pesan', $this->data())->json('kode_pesanan');
        $b = $this->postJson('/pesan', $this->data())->json('kode_pesanan');

        $this->assertNotSame($a, $b);
        $this->assertSame(2, Pesanan::count());
    }

    public function test_form_biasa_diarahkan_ke_halaman_bayar(): void
    {
        $response = $this->post('/pesan', $this->data());

        $p = Pesanan::firstOrFail();

        $response->assertRedirect(route('pesanan.bayar', $p->kode_pesanan));
    }

    public function test_halaman_bayar_non_tunai(): void
    {
        $kode = $this->postJson('/pesan', $this->data())->json('kode_pesanan');

        $this->get('/pesanan/'.$kode.'/bayar')
            ->assertOk()
            ->assertSee($kode)
            ->assertSee('130.000')
            ->assertSee('Unggah bukti')
            ->assertDontSee('081234567890');   // nomor HP tidak ditampilkan
    }

    public function test_halaman_bayar_cod_dan_kode_huruf_kecil_diterima(): void
    {
        $kode = $this->postJson('/pesan', $this->data(['metode_bayar' => 'cod']))->json('kode_pesanan');

        $this->get('/pesanan/'.strtolower($kode).'/bayar')
            ->assertOk()
            ->assertSee('Bayar di tempat');
    }

    public function test_kode_yang_tidak_ada_mengembalikan_404(): void
    {
        $this->get('/pesanan/OK-TIDAKADA/bayar')->assertNotFound();
    }
}
