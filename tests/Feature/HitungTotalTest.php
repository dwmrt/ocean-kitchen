<?php

namespace Tests\Feature;

use App\Models\Produk;
use App\Models\TarifOngkir;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HitungTotalTest extends TestCase
{
    use RefreshDatabase;

    private function buatProduk(string $nama, int $harga, int $minOrder = 1, string $status = Produk::STATUS_AKTIF): Produk
    {
        return Produk::create([
            'nama_produk' => $nama,
            'kategori' => 'Cake',
            'harga_jual' => $harga,
            'satuan' => 'Pcs',
            'min_order' => $minOrder,
            'status' => $status,
        ]);
    }

    private function buatTarif(int $biaya = 20000): TarifOngkir
    {
        return TarifOngkir::create(['wilayah' => 'Jarak 15-24 km', 'biaya' => $biaya]);
    }

    public function test_ambil_di_rumah_tanpa_ongkir(): void
    {
        $a = $this->buatProduk('Classic brownies', 65000);
        $b = $this->buatProduk('Cookies', 15000);

        $this->postJson('/api/hitung-total', [
            'items' => [
                ['id_produk' => $a->id_produk, 'jumlah' => 2],
                ['id_produk' => $b->id_produk, 'jumlah' => 1],
            ],
            'metode_terima' => 'ambil',
        ])
            ->assertOk()
            ->assertJsonPath('subtotal', 145000)
            ->assertJsonPath('ongkir', 0)
            ->assertJsonPath('total_bayar', 145000)
            ->assertJsonPath('items.0.subtotal', 130000)
            ->assertJsonPath('items.1.subtotal', 15000);
    }

    public function test_antar_menambah_ongkir_sesuai_tingkat_jarak(): void
    {
        $a = $this->buatProduk('Classic brownies', 65000);
        $tarif = $this->buatTarif(20000);

        $this->postJson('/api/hitung-total', [
            'items' => [['id_produk' => $a->id_produk, 'jumlah' => 2]],
            'metode_terima' => 'antar',
            'id_tarif' => $tarif->id_tarif,
        ])
            ->assertOk()
            ->assertJsonPath('subtotal', 130000)
            ->assertJsonPath('ongkir', 20000)
            ->assertJsonPath('total_bayar', 150000)
            ->assertJsonPath('tingkat_jarak', 'Jarak 15-24 km');
    }

    public function test_ambil_mengabaikan_id_tarif(): void
    {
        $a = $this->buatProduk('Cookies', 15000);
        $tarif = $this->buatTarif();

        $this->postJson('/api/hitung-total', [
            'items' => [['id_produk' => $a->id_produk, 'jumlah' => 1]],
            'metode_terima' => 'ambil',
            'id_tarif' => $tarif->id_tarif,
        ])
            ->assertOk()
            ->assertJsonPath('ongkir', 0)
            ->assertJsonPath('total_bayar', 15000);
    }

    public function test_produk_yang_sama_digabung_sebelum_dicek(): void
    {
        $a = $this->buatProduk('Cookies', 15000);

        $this->postJson('/api/hitung-total', [
            'items' => [
                ['id_produk' => $a->id_produk, 'jumlah' => 1],
                ['id_produk' => $a->id_produk, 'jumlah' => 2],
            ],
            'metode_terima' => 'ambil',
        ])
            ->assertOk()
            ->assertJsonCount(1, 'items')
            ->assertJsonPath('items.0.jumlah', 3)
            ->assertJsonPath('subtotal', 45000);
    }

    public function test_minimal_order_dipenuhi_dan_ditolak(): void
    {
        $donat = $this->buatProduk('Donat toping', 5000, 6);

        $this->postJson('/api/hitung-total', [
            'items' => [['id_produk' => $donat->id_produk, 'jumlah' => 5]],
            'metode_terima' => 'ambil',
        ])
            ->assertStatus(422)
            ->assertJsonPath('errors.items.0', 'Minimal pesan Donat toping adalah 6 Pcs.');

        $this->postJson('/api/hitung-total', [
            'items' => [['id_produk' => $donat->id_produk, 'jumlah' => 6]],
            'metode_terima' => 'ambil',
        ])
            ->assertOk()
            ->assertJsonPath('total_bayar', 30000);
    }

    public function test_produk_nonaktif_atau_tidak_ada_ditolak(): void
    {
        $nonaktif = $this->buatProduk('Menu lama', 40000, 1, Produk::STATUS_NONAKTIF);

        $this->postJson('/api/hitung-total', [
            'items' => [['id_produk' => $nonaktif->id_produk, 'jumlah' => 1]],
            'metode_terima' => 'ambil',
        ])->assertStatus(422)->assertJsonValidationErrors('items');

        $this->postJson('/api/hitung-total', [
            'items' => [['id_produk' => 99999, 'jumlah' => 1]],
            'metode_terima' => 'ambil',
        ])->assertStatus(422)->assertJsonValidationErrors('items');
    }

    public function test_antar_tanpa_atau_dengan_tingkat_jarak_salah_ditolak(): void
    {
        $a = $this->buatProduk('Cookies', 15000);

        $this->postJson('/api/hitung-total', [
            'items' => [['id_produk' => $a->id_produk, 'jumlah' => 1]],
            'metode_terima' => 'antar',
        ])->assertStatus(422)->assertJsonValidationErrors('id_tarif');

        $this->postJson('/api/hitung-total', [
            'items' => [['id_produk' => $a->id_produk, 'jumlah' => 1]],
            'metode_terima' => 'antar',
            'id_tarif' => 99999,
        ])->assertStatus(422)->assertJsonValidationErrors('id_tarif');
    }

    public function test_input_tidak_valid_ditolak(): void
    {
        $a = $this->buatProduk('Cookies', 15000);

        $this->postJson('/api/hitung-total', ['metode_terima' => 'ambil'])
            ->assertStatus(422)->assertJsonValidationErrors('items');

        $this->postJson('/api/hitung-total', ['items' => [], 'metode_terima' => 'ambil'])
            ->assertStatus(422)->assertJsonValidationErrors('items');

        $this->postJson('/api/hitung-total', [
            'items' => [['id_produk' => $a->id_produk, 'jumlah' => 0]],
            'metode_terima' => 'ambil',
        ])->assertStatus(422)->assertJsonValidationErrors('items.0.jumlah');

        $this->postJson('/api/hitung-total', [
            'items' => [['id_produk' => $a->id_produk, 'jumlah' => 'banyak']],
            'metode_terima' => 'ambil',
        ])->assertStatus(422)->assertJsonValidationErrors('items.0.jumlah');

        $this->postJson('/api/hitung-total', [
            'items' => [['id_produk' => $a->id_produk, 'jumlah' => 1]],
            'metode_terima' => 'kurir',
        ])->assertStatus(422)->assertJsonValidationErrors('metode_terima');
    }

    public function test_harga_selalu_dari_database_bukan_dari_browser(): void
    {
        $a = $this->buatProduk('Classic brownies', 65000);

        $this->postJson('/api/hitung-total', [
            'items' => [[
                'id_produk' => $a->id_produk,
                'jumlah' => 1,
                'harga_satuan' => 1,    // upaya mengakali harga
                'subtotal' => 1,
            ]],
            'metode_terima' => 'ambil',
            'total_bayar' => 1,
        ])
            ->assertOk()
            ->assertJsonPath('items.0.harga_satuan', 65000)
            ->assertJsonPath('total_bayar', 65000);
    }

    public function test_struktur_respons(): void
    {
        $a = $this->buatProduk('Cookies', 15000);

        $this->postJson('/api/hitung-total', [
            'items' => [['id_produk' => $a->id_produk, 'jumlah' => 1]],
            'metode_terima' => 'ambil',
        ])->assertJsonStructure([
            'items' => [['id_produk', 'nama_produk', 'harga_satuan', 'jumlah', 'subtotal']],
            'subtotal', 'metode_terima', 'tingkat_jarak', 'ongkir', 'total_bayar',
        ]);
    }
}
