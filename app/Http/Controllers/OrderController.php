<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateOrderRequest;
use App\Http\Requests\HitungTotalRequest;
use App\Services\CreateOrderService;
use App\Services\HitungTotalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class OrderController extends Controller
{
    /**
     * HitungTotal(): pratinjau total (produk + ongkir) untuk halaman Pesan.
     * Hanya menghitung, tidak menyimpan apa pun.
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

    /**
     * CreateOrder(): simpan pesanan.
     * - Form biasa: berhasil -> redirect ke halaman bayar; gagal -> kembali dengan pesan error.
     * - Fetch/JSON: berhasil -> 201 + data; gagal -> 422 { message, errors }.
     */
    public function createOrder(CreateOrderRequest $request, CreateOrderService $service): JsonResponse|RedirectResponse
    {
        $pesanan = $service->buat($request->validated());

        if ($request->expectsJson()) {
            return response()->json([
                'kode_pesanan' => $pesanan->kode_pesanan,
                'metode_bayar' => $pesanan->metode_bayar,
                'status_bayar' => $pesanan->status_bayar,
                'total_bayar' => (int) $pesanan->total_bayar,
                'batas_bayar' => $pesanan->batas_bayar?->toIso8601String(),
                'url_bayar' => route('pesanan.bayar', $pesanan->kode_pesanan),
            ], 201);
        }

        return redirect()->route('pesanan.bayar', $pesanan->kode_pesanan);
    }
}
