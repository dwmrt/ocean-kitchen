<?php

namespace App\Services;

use App\Models\Pesanan;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

/**
 * Menyimpan bukti pembayaran non-tunai.
 *
 * - File disimpan di disk PRIVAT ("local" = storage/app/private), dengan nama acak.
 *   Bukti berisi nama dan nomor rekening, jadi TIDAK boleh bisa dibuka lewat URL publik.
 *   Admin melihatnya lewat rute khusus yang dijaga login (Minggu 4).
 * - Boleh diunggah kalau status bayar "belum dibayar" atau "ditolak" (unggah ulang).
 * - Setelah berhasil: status bayar menjadi "menunggu verifikasi", alasan tolak dibersihkan,
 *   dan file bukti lama (kalau ada) dihapus.
 * - Mengunci baris pesanan selama proses, supaya dua unggahan bersamaan tidak saling menimpa.
 */
class UploadBuktiService
{
    public const DISK = 'local';

    /**
     * @throws ValidationException
     */
    public function unggah(Pesanan $pesanan, UploadedFile $file): Pesanan
    {
        return DB::transaction(function () use ($pesanan, $file) {
            $pesanan = Pesanan::whereKey($pesanan->getKey())->lockForUpdate()->firstOrFail();

            $this->pastikanBisaUnggah($pesanan);

            $lama = $pesanan->bukti_bayar;
            $path = $file->store('bukti', self::DISK);

            try {
                $pesanan->update([
                    'bukti_bayar' => $path,
                    'status_bayar' => Pesanan::BAYAR_MENUNGGU,
                    'alasan_tolak' => null,
                ]);
            } catch (\Throwable $e) {
                Storage::disk(self::DISK)->delete($path);   // jangan sisakan file yatim
                throw $e;
            }

            if ($lama && $lama !== $path) {
                Storage::disk(self::DISK)->delete($lama);
            }

            return $pesanan;
        });
    }

    private function pastikanBisaUnggah(Pesanan $p): void
    {
        $pesan = match (true) {
            $p->metode_bayar !== Pesanan::METODE_BAYAR_NON_TUNAI
                => 'Pesanan dengan bayar di tempat (COD) tidak perlu mengunggah bukti.',
            $p->status_pesanan === Pesanan::PESANAN_DIBATALKAN
                => 'Pesanan ini sudah dibatalkan.',
            $p->status_bayar === Pesanan::BAYAR_LUNAS
                => 'Pesanan ini sudah lunas.',
            $p->status_bayar === Pesanan::BAYAR_MENUNGGU
                => 'Bukti pembayaran sudah diunggah dan sedang diverifikasi.',
            $p->batas_bayar && $p->batas_bayar->isPast()
                => 'Batas waktu pembayaran sudah lewat.',
            default => null,
        };

        if ($pesan) {
            throw ValidationException::withMessages(['bukti_bayar' => $pesan]);
        }
    }
}
