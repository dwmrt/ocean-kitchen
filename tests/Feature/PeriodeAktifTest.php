<?php

namespace Tests\Feature;

use App\Models\PeriodePo;
use App\Services\PeriodePoService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PeriodeAktifTest extends TestCase
{
    use RefreshDatabase;

    private function buatPeriode(array $ubah = []): PeriodePo
    {
        return PeriodePo::create(array_merge([
            'nama_periode' => 'PO Tes',
            'tanggal_buka' => '2026-11-17',
            'tanggal_tutup' => '2026-11-22',
            'tanggal_terima' => '2026-11-27',
            'status' => PeriodePo::STATUS_AKTIF,
        ], $ubah));
    }

    public function test_tanpa_periode_dibuka_false(): void
    {
        $this->getJson('/api/periode-aktif')
            ->assertOk()
            ->assertJson(['dibuka' => false, 'periode' => null]);
    }

    public function test_periode_yang_sedang_dibuka(): void
    {
        $this->buatPeriode();
        $this->travelTo(Carbon::parse('2026-11-18 10:00:00'));

        $this->getJson('/api/periode-aktif')
            ->assertOk()
            ->assertJson([
                'dibuka' => true,
                'periode' => ['nama' => 'PO Tes', 'tanggal_tutup' => '2026-11-22'],
            ]);
    }

    public function test_hari_terakhir_masih_dibuka_dan_hari_berikutnya_tutup(): void
    {
        $this->buatPeriode();

        $this->travelTo(Carbon::parse('2026-11-22 23:30:00'));
        $this->getJson('/api/periode-aktif')->assertJson(['dibuka' => true]);

        $this->travelTo(Carbon::parse('2026-11-23 00:30:00'));
        $this->getJson('/api/periode-aktif')->assertJson(['dibuka' => false, 'periode' => null]);
    }

    public function test_periode_yang_ditutup_admin_tidak_dibuka(): void
    {
        $this->buatPeriode(['status' => PeriodePo::STATUS_DITUTUP]);
        $this->travelTo(Carbon::parse('2026-11-18 10:00:00'));

        $this->getJson('/api/periode-aktif')
            ->assertJson(['dibuka' => false, 'periode' => null]);
    }

    public function test_periode_yang_akan_datang_ditampilkan_tapi_belum_dibuka(): void
    {
        $this->buatPeriode();
        $this->travelTo(Carbon::parse('2026-11-10 09:00:00'));

        $this->getJson('/api/periode-aktif')
            ->assertOk()
            ->assertJson(['dibuka' => false, 'periode' => ['nama' => 'PO Tes']]);
    }

    public function test_yang_sedang_dibuka_didahulukan_daripada_yang_akan_datang(): void
    {
        $this->buatPeriode(['nama_periode' => 'PO Berjalan']);
        $this->buatPeriode([
            'nama_periode' => 'PO Berikutnya',
            'tanggal_buka' => '2026-12-01',
            'tanggal_tutup' => '2026-12-10',
            'tanggal_terima' => '2026-12-15',
        ]);
        $this->travelTo(Carbon::parse('2026-11-18 10:00:00'));

        $this->getJson('/api/periode-aktif')
            ->assertJson(['dibuka' => true, 'periode' => ['nama' => 'PO Berjalan']]);
    }

    public function test_service_mendeteksi_periode_bertumpuk(): void
    {
        $po = $this->buatPeriode();   // 17 sampai 22 Nov
        $service = new PeriodePoService();

        $this->assertTrue($service->bertumpuk(Carbon::parse('2026-11-20'), Carbon::parse('2026-11-25')));
        $this->assertTrue($service->bertumpuk(Carbon::parse('2026-11-22'), Carbon::parse('2026-11-30')), 'hari tutup ikut dihitung');
        $this->assertFalse($service->bertumpuk(Carbon::parse('2026-11-23'), Carbon::parse('2026-11-30')));
        $this->assertFalse($service->bertumpuk(Carbon::parse('2026-11-01'), Carbon::parse('2026-11-16')));

        // saat mengubah periode itu sendiri, jangan dibandingkan dengan dirinya
        $this->assertFalse($service->bertumpuk(Carbon::parse('2026-11-18'), Carbon::parse('2026-11-21'), $po->id_po));
    }

    public function test_periode_yang_ditutup_tidak_dihitung_bertumpuk(): void
    {
        $this->buatPeriode(['status' => PeriodePo::STATUS_DITUTUP]);

        $this->assertFalse(
            (new PeriodePoService())->bertumpuk(Carbon::parse('2026-11-18'), Carbon::parse('2026-11-20'))
        );
    }

    public function test_cek_rentang_tanggal(): void
    {
        $service = new PeriodePoService();

        $this->assertNull($service->cekRentang(
            Carbon::parse('2026-11-17'), Carbon::parse('2026-11-22'), Carbon::parse('2026-11-27')
        ));
        $this->assertNull($service->cekRentang(
            Carbon::parse('2026-11-17'), Carbon::parse('2026-11-22'), Carbon::parse('2026-11-22')
        ), 'tanggal terima boleh sama dengan tanggal tutup');
        $this->assertNotNull($service->cekRentang(
            Carbon::parse('2026-11-22'), Carbon::parse('2026-11-17'), Carbon::parse('2026-11-27')
        ));
        $this->assertNotNull($service->cekRentang(
            Carbon::parse('2026-11-17'), Carbon::parse('2026-11-22'), Carbon::parse('2026-11-20')
        ));
    }
}
