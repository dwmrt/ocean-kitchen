<?php

namespace App\Http\Controllers;

use App\Models\TarifOngkir;
use Illuminate\Http\JsonResponse;

class TarifOngkirController extends Controller
{
    /**
     * GetTarifOngkir(): daftar tingkat jarak untuk ongkir antar, diurutkan dari termurah.
     *
     * Aturan penjual: Rp 10.000 per 10 km, dibulatkan ke blok 10 km terdekat
     * (rumusnya ada di App\Services\OngkirService). Contoh tingkat:
     *   "Jarak 0-14 km" = 10.000, "Jarak 15-24 km" = 20.000, dst.
     *
     * - Frontend mengirim id_tarif saat pelanggan memilih tingkat jarak.
     * - Total bayar TETAP dihitung server (HitungTotal), bukan dari angka ini.
     * - "ambil_di_rumah" selalu 0: pesanan ambil tidak dikenai ongkir.
     */
    public function getTarifOngkir(): JsonResponse
    {
        $tarif = TarifOngkir::orderBy('biaya')->get()
            ->map(fn (TarifOngkir $t) => [
                'id_tarif' => $t->id_tarif,
                'wilayah' => $t->wilayah,
                'biaya' => (int) $t->biaya,
            ])
            ->values();

        return response()->json([
            'tarif' => $tarif,
            'ambil_di_rumah' => 0,
        ]);
    }
}
