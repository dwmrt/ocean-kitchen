<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PesananController extends Controller
{
    /**
     * Halaman setelah pesanan dibuat: ringkasan, total, dan cara bayar.
     * Kode pesanan berfungsi seperti kunci, jadi nomor HP tidak ditampilkan di sini.
     * Unggah bukti pembayaran menyusul (UploadBukti, Minggu 3).
     */
    public function bayar(string $kode): View
    {
        $pesanan = Pesanan::with('detail.produk')
            ->where('kode_pesanan', strtoupper($kode))
            ->firstOrFail();

        $qrisPath = config('toko.qris_path');
        $qrisUrl = ($qrisPath && Storage::disk('public')->exists($qrisPath))
            ? asset('storage/'.$qrisPath)
            : null;

        return view('pesanan.bayar', [
            'pesanan' => $pesanan,
            'qrisUrl' => $qrisUrl,
            'bank' => config('toko.bank'),
            'alamatAmbil' => config('toko.alamat_ambil'),
        ]);
    }
}
