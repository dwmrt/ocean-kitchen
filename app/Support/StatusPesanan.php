<?php

namespace App\Support;

use App\Models\Pesanan;

/**
 * Teks status yang ramah untuk pelanggan, satu tempat supaya konsisten di semua halaman.
 */
class StatusPesanan
{
    public static function bayar(Pesanan $p): string
    {
        if ($p->metode_bayar === Pesanan::METODE_BAYAR_COD) {
            return $p->status_bayar === Pesanan::BAYAR_LUNAS ? 'Lunas' : 'Bayar di tempat';
        }

        return match ($p->status_bayar) {
            Pesanan::BAYAR_BELUM => 'Belum dibayar',
            Pesanan::BAYAR_MENUNGGU => 'Menunggu verifikasi',
            Pesanan::BAYAR_LUNAS => 'Lunas',
            Pesanan::BAYAR_DITOLAK => 'Bukti ditolak',
            default => ucfirst(str_replace('_', ' ', (string) $p->status_bayar)),
        };
    }

    public static function pesanan(Pesanan $p): string
    {
        $antar = $p->metode_terima === Pesanan::METODE_ANTAR;

        return match ($p->status_pesanan) {
            Pesanan::PESANAN_BARU => 'Pesanan diterima',
            Pesanan::PESANAN_DIPROSES => 'Sedang diproses',
            Pesanan::PESANAN_SIAP => $antar ? 'Siap diantar' : 'Siap diambil',
            Pesanan::PESANAN_SELESAI => 'Selesai',
            Pesanan::PESANAN_DIBATALKAN => 'Dibatalkan',
            default => ucfirst(str_replace('_', ' ', (string) $p->status_pesanan)),
        };
    }

    /** Apakah pelanggan masih bisa membayar atau mengunggah (ulang) bukti? */
    public static function bisaBayar(Pesanan $p): bool
    {
        return $p->metode_bayar === Pesanan::METODE_BAYAR_NON_TUNAI
            && $p->status_pesanan !== Pesanan::PESANAN_DIBATALKAN
            && in_array($p->status_bayar, [Pesanan::BAYAR_BELUM, Pesanan::BAYAR_DITOLAK], true);
    }
}
