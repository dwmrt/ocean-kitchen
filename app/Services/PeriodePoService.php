<?php

namespace App\Services;

use App\Models\PeriodePo;
use Carbon\CarbonInterface;

class PeriodePoService
{
    /**
     * Apakah rentang tanggal bertumpuk dengan periode AKTIF lain?
     * Dua periode bertumpuk kalau ada minimal satu hari yang sama (hari tutup ikut dihitung).
     * $kecualiId dipakai saat mengubah periode (jangan membandingkan dengan dirinya sendiri).
     * Dipakai oleh halaman admin (kelola periode PO) nanti.
     */
    public function bertumpuk(CarbonInterface $buka, CarbonInterface $tutup, ?int $kecualiId = null): bool
    {
        return PeriodePo::aktif()
            ->when($kecualiId, fn ($q) => $q->where('id_po', '!=', $kecualiId))
            ->whereDate('tanggal_buka', '<=', $tutup->toDateString())
            ->whereDate('tanggal_tutup', '>=', $buka->toDateString())
            ->exists();
    }

    /**
     * Cek urutan tanggal. Mengembalikan pesan error, atau null kalau sudah benar.
     *   - tanggal tutup tidak boleh sebelum tanggal buka
     *   - tanggal terima tidak boleh sebelum tanggal tutup
     */
    public function cekRentang(CarbonInterface $buka, CarbonInterface $tutup, CarbonInterface $terima): ?string
    {
        $buka = $buka->copy()->startOfDay();
        $tutup = $tutup->copy()->startOfDay();
        $terima = $terima->copy()->startOfDay();

        if ($tutup->lt($buka)) {
            return 'Tanggal tutup tidak boleh sebelum tanggal buka.';
        }

        if ($terima->lt($tutup)) {
            return 'Tanggal terima tidak boleh sebelum tanggal tutup.';
        }

        return null;
    }
}
