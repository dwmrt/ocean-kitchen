<?php

namespace App\Http\Controllers;

use App\Http\Requests\HitungTotalRequest;
use App\Services\HitungTotalService;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    /**
     * HitungTotal(): pratinjau total (produk + ongkir) untuk halaman Pesan.
     * Hanya menghitung, tidak menyimpan apa pun. Kesalahan dikembalikan sebagai
     * 422 dengan { message, errors } (diurus Laravel otomatis).
     */
    public function hitungTotal(HitungTotalRequest $request, HitungTotalService $service): JsonResponse
    {
        $hasil = $service->hitung(
            $request->validated('items'),
            $request->validated('metode_terima'),
            $request->validated('id_tarif'),
        );

        return response()->json($hasil);
    }
}
