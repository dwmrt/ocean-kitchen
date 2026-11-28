<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Support\StatusPesanan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PesananController extends Controller
{
    /**
     * Halaman setelah pesanan dibuat: ringkasan, total, cara bayar, dan unggah bukti.
     * Kode pesanan berfungsi seperti kunci, jadi nomor HP tidak ditampilkan di sini.
     */
    public function bayar(string $kode): View
    {
        $pesanan = Pesanan::with('detail.produk')
            ->where('kode_pesanan', $this->rapikanKode($kode))
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

    /** Form untuk memasukkan kode pesanan. */
    public function cekStatus(): View
    {
        return view('pesanan.cek-status');
    }

    /**
     * Cek kode pesanan lalu arahkan ke halaman status.
     * Dibatasi jumlah percobaannya (throttle di rute) supaya kode tidak bisa ditebak-tebak.
     * Pesan error sengaja sama untuk semua kode yang tidak ada.
     */
    public function cari(Request $request): RedirectResponse
    {
        $kode = $this->rapikanKode((string) $request->input('kode'));

        if ($kode === '') {
            return back()->withErrors(['kode' => 'Masukkan kode pesanan Anda.']);
        }

        if (! Pesanan::where('kode_pesanan', $kode)->exists()) {
            return back()
                ->withErrors(['kode' => 'Kode pesanan tidak ditemukan. Periksa kembali kode Anda.'])
                ->withInput();
        }

        return redirect()->route('pesanan.status', $kode);
    }

    /**
     * Status pesanan untuk pelanggan.
     * TIDAK menampilkan nama, nomor HP, maupun alamat lengkap pelanggan.
     */
    public function status(string $kode): View
    {
        $pesanan = Pesanan::with('detail.produk')
            ->where('kode_pesanan', $this->rapikanKode($kode))
            ->firstOrFail();

        return view('pesanan.status', [
            'pesanan' => $pesanan,
            'labelBayar' => StatusPesanan::bayar($pesanan),
            'labelPesanan' => StatusPesanan::pesanan($pesanan),
            'bisaBayar' => StatusPesanan::bisaBayar($pesanan),
            'alamatAmbil' => config('toko.alamat_ambil'),
        ]);
    }

    /** Huruf besar dan tanpa spasi, supaya "ok-7k2m 9qxa" tetap dikenali. */
    private function rapikanKode(string $kode): string
    {
        return strtoupper(preg_replace('/\s+/', '', $kode));
    }
}
