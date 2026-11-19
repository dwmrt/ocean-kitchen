<?php

namespace App\Services;

use App\Models\Pesanan;
use App\Models\Produk;
use App\Models\TarifOngkir;
use Illuminate\Validation\ValidationException;

/**
 * Satu-satunya tempat total pesanan dihitung. Dipakai HitungTotal() (pratinjau di halaman
 * Pesan) dan nanti CreateOrder() (saat pesanan disimpan), supaya angkanya selalu sama.
 *
 * Aturan:
 *  - Harga selalu dari database, tidak pernah dari browser.
 *  - Hanya produk aktif yang boleh dipesan.
 *  - Jumlah per produk minimal sebesar min_order produk itu.
 *  - Produk yang sama di beberapa baris digabung dulu sebelum dicek.
 *  - Ambil di rumah: ongkir 0. Antar: ongkir = biaya tingkat jarak yang dipilih.
 *  - total_bayar = subtotal + ongkir (dipakai untuk QRIS, transfer, maupun COD).
 *
 * Semua uang berupa rupiah utuh (integer).
 */
class HitungTotalService
{
    /**
     * @param  array<int, array{id_produk: int|string, jumlah: int|string}>  $items
     *
     * @throws ValidationException
     */
    public function hitung(array $items, string $metodeTerima, ?int $idTarif = null): array
    {
        // gabungkan baris dengan produk yang sama
        $jumlahPerProduk = [];
        foreach ($items as $item) {
            $id = (int) $item['id_produk'];
            $jumlahPerProduk[$id] = ($jumlahPerProduk[$id] ?? 0) + (int) $item['jumlah'];
        }

        $produk = Produk::aktif()
            ->whereIn('id_produk', array_keys($jumlahPerProduk))
            ->get()
            ->keyBy('id_produk');

        $baris = [];
        $subtotal = 0;

        foreach ($jumlahPerProduk as $id => $jumlah) {
            $p = $produk->get($id);

            if (! $p) {
                throw ValidationException::withMessages([
                    'items' => "Produk dengan ID {$id} tidak tersedia.",
                ]);
            }

            if ($jumlah < $p->min_order) {
                throw ValidationException::withMessages([
                    'items' => "Minimal pesan {$p->nama_produk} adalah {$p->min_order} {$p->satuan}.",
                ]);
            }

            $harga = (int) $p->harga_jual;
            $jumlahBaris = $harga * $jumlah;
            $subtotal += $jumlahBaris;

            $baris[] = [
                'id_produk' => $p->id_produk,
                'nama_produk' => $p->nama_produk,
                'harga_satuan' => $harga,
                'jumlah' => $jumlah,
                'subtotal' => $jumlahBaris,
            ];
        }

        $ongkir = 0;
        $tingkatJarak = null;

        if ($metodeTerima === Pesanan::METODE_ANTAR) {
            $tarif = $idTarif ? TarifOngkir::find($idTarif) : null;

            if (! $tarif) {
                throw ValidationException::withMessages([
                    'id_tarif' => 'Tingkat jarak tidak valid.',
                ]);
            }

            $ongkir = (int) $tarif->biaya;
            $tingkatJarak = $tarif->wilayah;
        }

        return [
            'items' => $baris,
            'subtotal' => $subtotal,
            'metode_terima' => $metodeTerima,
            'tingkat_jarak' => $tingkatJarak,
            'ongkir' => $ongkir,
            'total_bayar' => $subtotal + $ongkir,
        ];
    }
}
