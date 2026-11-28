<?php

namespace Tests\Feature;

use App\Models\PeriodePo;
use App\Models\Pesanan;
use App\Models\Produk;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CekStatusTest extends TestCase
{
    use RefreshDatabase;

    private PeriodePo $po;
    private Produk $produk;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->travelTo(Carbon::parse('2026-11-18 10:00:00'));

        $this->po = PeriodePo::create([
            'nama_periode' => 'PO Tes',
            'tanggal_buka' => '2026-11-17',
            'tanggal_tutup' => '2026-11-22',
            'tanggal_terima' => '2026-11-27',
            'status' => PeriodePo::STATUS_AKTIF,
        ]);

        $this->produk = Produk::create([
            'nama_produk' => 'Classic brownies', 'kategori' => 'Brownies', 'harga_jual' => 65000,
            'satuan' => 'Loyang', 'min_order' => 1, 'status' => Produk::STATUS_AKTIF,
        ]);
    }

    private function buatPesanan(array $ubah = []): Pesanan
    {
        $pesanan = Pesanan::create(array_merge([
            'kode_pesanan' => 'OK-TEST2345',
            'id_po' => $this->po->id_po,
            'nama_pelanggan' => 'Sinta Dewi',
            'no_hp' => '081234567890',
            'alamat' => 'Jl. Rahasia No. 99, RT 01 RW 02',
            'metode_terima' => 'antar',
            'jadwal_terima' => '2026-11-27 14:00:00',
            'ongkir' => 10000,
            'total_bayar' => 140000,
            'metode_bayar' => Pesanan::METODE_BAYAR_NON_TUNAI,
            'batas_bayar' => now()->addHours(24),
            'status_bayar' => Pesanan::BAYAR_BELUM,
            'status_pesanan' => Pesanan::PESANAN_BARU,
        ], $ubah));

        $pesanan->detail()->create([
            'id_produk' => $this->produk->id_produk,
            'jumlah' => 2,
            'harga_satuan' => 65000,
            'subtotal' => 130000,
        ]);

        return $pesanan;
    }

    public function test_halaman_cek_status_tampil(): void
    {
        $this->get('/cek-status')
            ->assertOk()
            ->assertSee('name="kode"', false);
    }

    public function test_kode_benar_diarahkan_ke_halaman_status(): void
    {
        $this->buatPesanan();

        $this->post('/cek-status', ['kode' => 'OK-TEST2345'])
            ->assertRedirect(route('pesanan.status', 'OK-TEST2345'));
    }

    public function test_kode_huruf_kecil_dan_berspasi_dirapikan(): void
    {
        $this->buatPesanan();

        $this->post('/cek-status', ['kode' => '  ok-test 2345 '])
            ->assertRedirect(route('pesanan.status', 'OK-TEST2345'));

        $this->get('/pesanan/ok-test2345')->assertOk();
    }

    public function test_kode_tidak_ada_atau_kosong_menghasilkan_error(): void
    {
        $this->post('/cek-status', ['kode' => 'OK-TIDAKADA'])->assertSessionHasErrors('kode');
        $this->post('/cek-status', ['kode' => ''])->assertSessionHasErrors('kode');
        $this->get('/pesanan/OK-TIDAKADA')->assertNotFound();
    }

    public function test_pencarian_dibatasi_jumlah_percobaannya(): void
    {
        for ($i = 0; $i < 10; $i++) {
            $this->post('/cek-status', ['kode' => 'OK-SALAH'.$i]);
        }

        $this->post('/cek-status', ['kode' => 'OK-SALAH99'])->assertStatus(429);
    }

    public function test_status_menampilkan_ringkasan_tanpa_data_pribadi(): void
    {
        $this->buatPesanan();

        $this->get('/pesanan/OK-TEST2345')
            ->assertOk()
            ->assertSee('OK-TEST2345')
            ->assertSee('Pesanan diterima')
            ->assertSee('Belum dibayar')
            ->assertSee('Classic brownies')
            ->assertSee('140.000')
            ->assertDontSee('Sinta Dewi')
            ->assertDontSee('081234567890')
            ->assertDontSee('Jl. Rahasia No. 99');
    }

    public function test_pesanan_belum_dibayar_punya_tautan_bayar(): void
    {
        $this->buatPesanan();

        $this->get('/pesanan/OK-TEST2345')
            ->assertSee(route('pesanan.bayar', 'OK-TEST2345'));
    }

    public function test_status_menunggu_verifikasi_dan_lunas_tanpa_tautan_bayar(): void
    {
        $pesanan = $this->buatPesanan(['status_bayar' => Pesanan::BAYAR_MENUNGGU]);

        $this->get('/pesanan/OK-TEST2345')
            ->assertSee('Menunggu verifikasi')
            ->assertDontSee(route('pesanan.bayar', 'OK-TEST2345'));

        $pesanan->update(['status_bayar' => Pesanan::BAYAR_LUNAS, 'status_pesanan' => Pesanan::PESANAN_DIPROSES]);

        $this->get('/pesanan/OK-TEST2345')
            ->assertSee('Lunas')
            ->assertSee('Sedang diproses')
            ->assertDontSee(route('pesanan.bayar', 'OK-TEST2345'));
    }

    public function test_bukti_ditolak_menampilkan_alasan_dan_tautan_unggah_ulang(): void
    {
        $this->buatPesanan([
            'status_bayar' => Pesanan::BAYAR_DITOLAK,
            'alasan_tolak' => 'Nominal tidak sesuai',
        ]);

        $this->get('/pesanan/OK-TEST2345')
            ->assertSee('Bukti ditolak')
            ->assertSee('Nominal tidak sesuai')
            ->assertSee(route('pesanan.bayar', 'OK-TEST2345'));
    }

    public function test_pesanan_dibatalkan_menampilkan_alasan_tanpa_tautan_bayar(): void
    {
        $this->buatPesanan([
            'status_pesanan' => Pesanan::PESANAN_DIBATALKAN,
            'alasan_batal' => 'Batas waktu pembayaran terlewati',
        ]);

        $this->get('/pesanan/OK-TEST2345')
            ->assertSee('Dibatalkan')
            ->assertSee('Batas waktu pembayaran terlewati')
            ->assertDontSee(route('pesanan.bayar', 'OK-TEST2345'));
    }

    public function test_cod_menampilkan_bayar_di_tempat_tanpa_tautan_bayar(): void
    {
        $this->buatPesanan(['metode_bayar' => Pesanan::METODE_BAYAR_COD, 'batas_bayar' => null]);

        $this->get('/pesanan/OK-TEST2345')
            ->assertOk()
            ->assertSee('Bayar di tempat')
            ->assertDontSee(route('pesanan.bayar', 'OK-TEST2345'));
    }

    public function test_ambil_di_rumah_menampilkan_alamat_pengambilan_dari_config(): void
    {
        config(['toko.alamat_ambil' => 'Perumahan Contoh Blok A No. 1']);
        $this->buatPesanan(['metode_terima' => 'ambil', 'alamat' => null, 'ongkir' => 0, 'total_bayar' => 130000]);

        $this->get('/pesanan/OK-TEST2345')
            ->assertSee('Perumahan Contoh Blok A No. 1');
    }

    public function test_siap_diantar_dan_siap_diambil(): void
    {
        $pesanan = $this->buatPesanan(['status_pesanan' => Pesanan::PESANAN_SIAP]);
        $this->get('/pesanan/OK-TEST2345')->assertSee('Siap diantar');

        $pesanan->update(['metode_terima' => 'ambil', 'alamat' => null]);
        $this->get('/pesanan/OK-TEST2345')->assertSee('Siap diambil');
    }
}
