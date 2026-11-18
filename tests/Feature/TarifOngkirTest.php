<?php

namespace Tests\Feature;

use App\Models\TarifOngkir;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TarifOngkirTest extends TestCase
{
    use RefreshDatabase;

    public function test_daftar_tarif_diurutkan_dari_termurah(): void
    {
        // sengaja dimasukkan tidak berurutan
        TarifOngkir::create(['wilayah' => 'Jarak 25-34 km', 'biaya' => 30000]);
        TarifOngkir::create(['wilayah' => 'Jarak 0-14 km', 'biaya' => 10000]);
        TarifOngkir::create(['wilayah' => 'Jarak 15-24 km', 'biaya' => 20000]);

        $this->getJson('/api/tarif-ongkir')
            ->assertOk()
            ->assertJsonCount(3, 'tarif')
            ->assertJsonPath('tarif.0.wilayah', 'Jarak 0-14 km')
            ->assertJsonPath('tarif.0.biaya', 10000)
            ->assertJsonPath('tarif.1.biaya', 20000)
            ->assertJsonPath('tarif.2.biaya', 30000);
    }

    public function test_ambil_di_rumah_gratis(): void
    {
        $this->getJson('/api/tarif-ongkir')
            ->assertOk()
            ->assertJsonPath('ambil_di_rumah', 0);
    }

    public function test_struktur_tiap_tarif(): void
    {
        $tarif = TarifOngkir::create(['wilayah' => 'Jarak 0-14 km', 'biaya' => 10000]);

        $this->getJson('/api/tarif-ongkir')
            ->assertOk()
            ->assertJsonStructure(['tarif' => [['id_tarif', 'wilayah', 'biaya']], 'ambil_di_rumah'])
            ->assertJsonPath('tarif.0.id_tarif', $tarif->id_tarif);
    }

    public function test_tanpa_data_mengembalikan_daftar_kosong(): void
    {
        $this->getJson('/api/tarif-ongkir')
            ->assertOk()
            ->assertExactJson(['tarif' => [], 'ambil_di_rumah' => 0]);
    }
}
