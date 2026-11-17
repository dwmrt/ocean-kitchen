<?php

namespace App\Http\Controllers;

use App\Models\PeriodePo;
use Illuminate\Http\JsonResponse;

class PeriodePoController extends Controller
{
    /**
     * GetPeriodeAktif(): periode PO untuk banner dan halaman pesan.
     *
     * "dibuka" dihitung di server dari tanggal hari ini dan status periode,
     * tidak pernah dari browser. Hari tutup masih dihitung dibuka sampai akhir hari.
     *
     * Isi "periode":
     *   - periode yang sedang dibuka, atau
     *   - kalau tidak ada, periode aktif terdekat yang akan datang (dibuka = false), atau
     *   - null kalau tidak ada keduanya.
     */
    public function getPeriodeAktif(): JsonResponse
    {
        $po = PeriodePo::terpilih();

        if (! $po) {
            return response()->json([
                'dibuka' => false,
                'periode' => null,
                'pesan' => 'Belum ada periode PO.',
            ]);
        }

        return response()->json([
            'dibuka' => $po->dibuka,
            'periode' => [
                'id_po' => $po->id_po,
                'nama' => $po->nama_periode,
                'tanggal_buka' => $po->tanggal_buka->toDateString(),
                'tanggal_tutup' => $po->tanggal_tutup->toDateString(),
                'tanggal_terima' => $po->tanggal_terima->toDateString(),
            ],
            'pesan' => $po->dibuka
                ? 'PO dibuka sampai '.$po->tanggal_tutup->translatedFormat('j F Y').'.'
                : 'PO dibuka pada '.$po->tanggal_buka->translatedFormat('j F Y').'.',
        ]);
    }
}
