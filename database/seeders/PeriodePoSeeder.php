<?php

namespace Database\Seeders;

use App\Models\PeriodePo;
use Illuminate\Database\Seeder;

class PeriodePoSeeder extends Seeder
{
    // DATA CONTOH: satu periode yang sedang dibuka, supaya katalog dan form pesan bisa diuji.
    public function run(): void
    {
        $hariIni = now()->startOfDay();

        PeriodePo::updateOrCreate(
            ['nama_periode' => 'PO Contoh'],
            [
                'tanggal_buka' => $hariIni->copy(),
                'tanggal_tutup' => $hariIni->copy()->addDays(5),
                'tanggal_terima' => $hariIni->copy()->addDays(7),
                'status' => PeriodePo::STATUS_AKTIF,
            ]
        );
    }
}
