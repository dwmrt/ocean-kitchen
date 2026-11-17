<?php

namespace App\Services;

use InvalidArgumentException;

/**
 * Aturan ongkir antar dari pemilik toko:
 *   - Rp 10.000 per blok 10 km, berlaku untuk semua wilayah.
 *   - Jarak dibulatkan ke blok 10 km TERDEKAT; sisa 5 km atau lebih dibulatkan ke atas.
 *       10 km = 10.000 | 11-14 km = 10.000 | 15-24 km = 20.000 | 25-34 km = 30.000 | dst.
 *   - Minimal 1 blok (Rp 10.000) untuk pesanan antar.
 *   - Pesanan "ambil di rumah" tidak memakai service ini (ongkir 0).
 *
 * Murni hitungan (tidak butuh database), jadi mudah diuji.
 * Sumber jarak boleh apa saja: pilihan tingkat jarak, estimasi dari titik peta, atau API rute.
 */
class OngkirService
{
    public function __construct(
        private int $tarifPerBlok = 10000,       // Rp per blok
        private int $panjangBlokKm = 10,         // 1 blok = 10 km
        private float $ambangPembulatanKm = 5.0, // sisa >= 5 km dihitung 1 blok penuh
    ) {
    }

    public function hitung(float $jarakKm): int
    {
        if ($jarakKm < 0) {
            throw new InvalidArgumentException('Jarak tidak boleh negatif.');
        }

        $jarakKm = round($jarakKm, 2);

        $blok = (int) floor(
            ($jarakKm + $this->panjangBlokKm - $this->ambangPembulatanKm) / $this->panjangBlokKm
        );

        return max(1, $blok) * $this->tarifPerBlok;
    }
}
