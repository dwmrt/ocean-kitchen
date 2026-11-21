<?php

namespace App\Services;

use App\Models\PeriodePo;
use App\Models\Pesanan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Menyimpan pesanan. Total SELALU dihitung ulang di sini lewat HitungTotalService
 * (yang sama dengan pratinjau di halaman Pesan), tidak pernah dari browser.
 *
 * Aturan:
 *  - Hanya diterima kalau ada periode PO yang sedang dibuka.
 *  - Jadwal terima harus pada tanggal terima periode itu.
 *  - Produk aktif, minimal order, dan ongkir dicek oleh HitungTotalService.
 *  - Non-tunai: batas bayar 24 jam. COD: tanpa batas bayar.
 *  - Status awal: belum_dibayar dan baru.
 *  - pesanan dan detail_pesanan disimpan dalam satu transaksi (gagal satu, gagal semua).
 *  - harga_satuan dan subtotal disalin ke detail_pesanan, jadi perubahan harga
 *    di kemudian hari tidak mengubah pesanan lama.
 */
class CreateOrderService
{
    public const BATAS_BAYAR_JAM = 24;

    public function __construct(private HitungTotalService $hitungTotal)
    {
    }

    /**
     * @param  array  $data  hasil CreateOrderRequest::validated()
     *
     * @throws ValidationException
     */
    public function buat(array $data): Pesanan
    {
        $po = PeriodePo::dibuka()->orderBy('tanggal_buka')->first();

        if (! $po) {
            throw ValidationException::withMessages([
                'periode' => 'Maaf, pemesanan sedang ditutup. Silakan tunggu periode PO berikutnya.',
            ]);
        }

        $jadwal = Carbon::parse($data['jadwal_terima']);

        if (! $jadwal->isSameDay($po->tanggal_terima)) {
            throw ValidationException::withMessages([
                'jadwal_terima' => 'Jadwal terima harus pada tanggal '.$po->tanggal_terima->translatedFormat('j F Y').'.',
            ]);
        }

        $antar = $data['metode_terima'] === Pesanan::METODE_ANTAR;

        $hasil = $this->hitungTotal->hitung(
            $data['items'],
            $data['metode_terima'],
            $data['id_tarif'] ?? null,
        );

        $nonTunai = $data['metode_bayar'] === Pesanan::METODE_BAYAR_NON_TUNAI;

        return DB::transaction(function () use ($data, $po, $jadwal, $hasil, $antar, $nonTunai) {
            $pesanan = Pesanan::create([
                'kode_pesanan' => $this->buatKode(),
                'id_po' => $po->id_po,
                'id_tarif' => $antar ? ($data['id_tarif'] ?? null) : null,
                'nama_pelanggan' => $data['nama_pelanggan'],
                'no_hp' => $data['no_hp'],
                'alamat' => $antar ? $data['alamat'] : null,
                'metode_terima' => $data['metode_terima'],
                'jadwal_terima' => $jadwal,
                'ongkir' => $hasil['ongkir'],
                'total_bayar' => $hasil['total_bayar'],
                'metode_bayar' => $data['metode_bayar'],
                'batas_bayar' => $nonTunai ? now()->addHours(self::BATAS_BAYAR_JAM) : null,
                'status_bayar' => Pesanan::BAYAR_BELUM,
                'status_pesanan' => Pesanan::PESANAN_BARU,
            ]);

            foreach ($hasil['items'] as $baris) {
                $pesanan->detail()->create([
                    'id_produk' => $baris['id_produk'],
                    'jumlah' => $baris['jumlah'],
                    'harga_satuan' => $baris['harga_satuan'],
                    'subtotal' => $baris['subtotal'],
                ]);
            }

            return $pesanan->load('detail');
        });
    }

    /**
     * Kode acak yang tidak berurutan dan sulit ditebak, contoh: OK-7K2M9QXA.
     * Tanpa huruf/angka yang mirip (I, O, 0, 1).
     */
    private function buatKode(): string
    {
        $karakter = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

        do {
            $kode = 'OK-';
            for ($i = 0; $i < 8; $i++) {
                $kode .= $karakter[random_int(0, strlen($karakter) - 1)];
            }
        } while (Pesanan::where('kode_pesanan', $kode)->exists());

        return $kode;
    }
}
