<?php

namespace Tests\Feature;

use App\Models\PeriodePo;
use App\Models\Pesanan;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UploadBuktiTest extends TestCase
{
    use RefreshDatabase;

    private PeriodePo $po;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        Storage::fake('local');    // disk privat tempat bukti disimpan
        Storage::fake('public');
        $this->travelTo(Carbon::parse('2026-11-18 10:00:00'));

        $this->po = PeriodePo::create([
            'nama_periode' => 'PO Tes',
            'tanggal_buka' => '2026-11-17',
            'tanggal_tutup' => '2026-11-22',
            'tanggal_terima' => '2026-11-27',
            'status' => PeriodePo::STATUS_AKTIF,
        ]);
    }

    private function buatPesanan(array $ubah = []): Pesanan
    {
        return Pesanan::create(array_merge([
            'kode_pesanan' => 'OK-TEST2345',
            'id_po' => $this->po->id_po,
            'nama_pelanggan' => 'Sinta Dewi',
            'no_hp' => '081234567890',
            'metode_terima' => 'ambil',
            'jadwal_terima' => '2026-11-27 14:00:00',
            'ongkir' => 0,
            'total_bayar' => 130000,
            'metode_bayar' => Pesanan::METODE_BAYAR_NON_TUNAI,
            'batas_bayar' => now()->addHours(24),
            'status_bayar' => Pesanan::BAYAR_BELUM,
            'status_pesanan' => Pesanan::PESANAN_BARU,
        ], $ubah));
    }

    private function unggah(string $kode, $file)
    {
        return $this->post('/pesanan/'.$kode.'/bukti', ['bukti_bayar' => $file], ['Accept' => 'application/json']);
    }

    public function test_unggah_bukti_berhasil_dan_tersimpan_di_disk_privat(): void
    {
        $this->buatPesanan();

        $this->unggah('OK-TEST2345', UploadedFile::fake()->image('struk.jpg', 600, 800))
            ->assertOk()
            ->assertJsonPath('status_bayar', 'menunggu_verifikasi');

        $p = Pesanan::firstOrFail();

        $this->assertSame('menunggu_verifikasi', $p->status_bayar);
        $this->assertNotNull($p->bukti_bayar);
        Storage::disk('local')->assertExists($p->bukti_bayar);
        Storage::disk('public')->assertMissing($p->bukti_bayar);   // tidak boleh publik
        $this->assertStringNotContainsString('struk', $p->bukti_bayar);   // nama file diacak
    }

    public function test_unggah_ulang_setelah_ditolak_mengganti_file_dan_membersihkan_alasan(): void
    {
        Storage::disk('local')->put('bukti/lama.jpg', 'isi lama');
        $this->buatPesanan([
            'status_bayar' => Pesanan::BAYAR_DITOLAK,
            'alasan_tolak' => 'Nominal tidak sesuai',
            'bukti_bayar' => 'bukti/lama.jpg',
        ]);

        $this->unggah('OK-TEST2345', UploadedFile::fake()->image('baru.png', 500, 500))->assertOk();

        $p = Pesanan::firstOrFail();

        $this->assertSame('menunggu_verifikasi', $p->status_bayar);
        $this->assertNull($p->alasan_tolak);
        $this->assertNotSame('bukti/lama.jpg', $p->bukti_bayar);
        Storage::disk('local')->assertMissing('bukti/lama.jpg');
        Storage::disk('local')->assertExists($p->bukti_bayar);
    }

    public function test_cod_tidak_menerima_bukti(): void
    {
        $this->buatPesanan(['metode_bayar' => Pesanan::METODE_BAYAR_COD, 'batas_bayar' => null]);

        $this->unggah('OK-TEST2345', UploadedFile::fake()->image('struk.jpg'))
            ->assertStatus(422)
            ->assertJsonValidationErrors('bukti_bayar');

        $this->assertNull(Pesanan::firstOrFail()->bukti_bayar);
    }

    public function test_status_yang_tidak_boleh_unggah(): void
    {
        $this->buatPesanan(['status_bayar' => Pesanan::BAYAR_MENUNGGU]);
        $this->unggah('OK-TEST2345', UploadedFile::fake()->image('a.jpg'))
            ->assertStatus(422)->assertJsonValidationErrors('bukti_bayar');

        Pesanan::firstOrFail()->update(['status_bayar' => Pesanan::BAYAR_LUNAS]);
        $this->unggah('OK-TEST2345', UploadedFile::fake()->image('a.jpg'))
            ->assertStatus(422)->assertJsonValidationErrors('bukti_bayar');

        Pesanan::firstOrFail()->update([
            'status_bayar' => Pesanan::BAYAR_BELUM,
            'status_pesanan' => Pesanan::PESANAN_DIBATALKAN,
        ]);
        $this->unggah('OK-TEST2345', UploadedFile::fake()->image('a.jpg'))
            ->assertStatus(422)->assertJsonValidationErrors('bukti_bayar');
    }

    public function test_batas_bayar_yang_sudah_lewat_ditolak(): void
    {
        $this->buatPesanan();
        $this->travelTo(Carbon::parse('2026-11-19 10:00:01'));   // 24 jam + 1 detik kemudian

        $this->unggah('OK-TEST2345', UploadedFile::fake()->image('a.jpg'))
            ->assertStatus(422)
            ->assertJsonValidationErrors('bukti_bayar');

        $this->assertSame('belum_dibayar', Pesanan::firstOrFail()->status_bayar);
    }

    public function test_file_yang_tidak_valid_ditolak(): void
    {
        $this->buatPesanan();

        // bukan gambar
        $this->unggah('OK-TEST2345', UploadedFile::fake()->create('bukti.pdf', 100, 'application/pdf'))
            ->assertStatus(422)->assertJsonValidationErrors('bukti_bayar');

        // terlalu besar (6 MB)
        $this->unggah('OK-TEST2345', UploadedFile::fake()->image('besar.jpg')->size(6000))
            ->assertStatus(422)->assertJsonValidationErrors('bukti_bayar');

        // tanpa file
        $this->post('/pesanan/OK-TEST2345/bukti', [], ['Accept' => 'application/json'])
            ->assertStatus(422)->assertJsonValidationErrors('bukti_bayar');

        $p = Pesanan::firstOrFail();
        $this->assertSame('belum_dibayar', $p->status_bayar);
        $this->assertNull($p->bukti_bayar);
    }

    public function test_file_teks_yang_menyamar_sebagai_jpg_ditolak(): void
    {
        $this->buatPesanan();

        // Memakai file ASLI, bukan UploadedFile::fake(): file fake dari Laravel menentukan
        // tipenya dari NAMA file, sedangkan file asli dibaca dari ISInya (seperti di server).
        $path = tempnam(sys_get_temp_dir(), 'bukti');
        file_put_contents($path, 'ini bukan gambar, hanya teks');
        $file = new UploadedFile($path, 'palsu.jpg', 'image/jpeg', null, true);

        try {
            $this->unggah('OK-TEST2345', $file)
                ->assertStatus(422)
                ->assertJsonValidationErrors('bukti_bayar');
        } finally {
            @unlink($path);
        }

        $this->assertNull(Pesanan::firstOrFail()->bukti_bayar);
    }

    public function test_gambar_png_asli_diterima(): void
    {
        $this->buatPesanan();

        // PNG nyata dibuat dengan GD, supaya jalur pembacaan isi file ikut teruji.
        $path = tempnam(sys_get_temp_dir(), 'bukti');
        $gambar = imagecreatetruecolor(50, 50);
        imagepng($gambar, $path);
        $file = new UploadedFile($path, 'struk.png', 'image/png', null, true);

        try {
            $this->unggah('OK-TEST2345', $file)
                ->assertOk()
                ->assertJsonPath('status_bayar', 'menunggu_verifikasi');
        } finally {
            @unlink($path);
        }

        Storage::disk('local')->assertExists(Pesanan::firstOrFail()->bukti_bayar);
    }

    public function test_kode_tidak_ada_mengembalikan_404(): void
    {
        $this->unggah('OK-TIDAKADA', UploadedFile::fake()->image('a.jpg'))->assertNotFound();
    }

    public function test_kode_huruf_kecil_diterima(): void
    {
        $this->buatPesanan();

        $this->unggah('ok-test2345', UploadedFile::fake()->image('a.jpg'))->assertOk();
    }

    public function test_form_biasa_kembali_ke_halaman_bayar_dengan_pesan(): void
    {
        $this->buatPesanan();

        $this->post('/pesanan/OK-TEST2345/bukti', ['bukti_bayar' => UploadedFile::fake()->image('a.jpg')])
            ->assertRedirect(route('pesanan.bayar', 'OK-TEST2345'))
            ->assertSessionHas('status');
    }

    public function test_halaman_bayar_menampilkan_form_sesuai_status(): void
    {
        $this->buatPesanan();
        $this->get('/pesanan/OK-TEST2345/bayar')
            ->assertOk()
            ->assertSee('name="bukti_bayar"', false);

        Pesanan::firstOrFail()->update([
            'status_bayar' => Pesanan::BAYAR_DITOLAK,
            'alasan_tolak' => 'Nominal tidak sesuai',
        ]);
        $this->get('/pesanan/OK-TEST2345/bayar')
            ->assertOk()
            ->assertSee('Nominal tidak sesuai')
            ->assertSee('name="bukti_bayar"', false);

        Pesanan::firstOrFail()->update(['status_bayar' => Pesanan::BAYAR_MENUNGGU]);
        $this->get('/pesanan/OK-TEST2345/bayar')
            ->assertOk()
            ->assertSee('sedang diverifikasi')
            ->assertDontSee('name="bukti_bayar"', false);
    }

    public function test_halaman_bayar_cod_tanpa_form_unggah(): void
    {
        $this->buatPesanan(['metode_bayar' => Pesanan::METODE_BAYAR_COD, 'batas_bayar' => null]);

        $this->get('/pesanan/OK-TEST2345/bayar')
            ->assertOk()
            ->assertSee('Bayar di tempat')
            ->assertDontSee('name="bukti_bayar"', false);
    }
}
