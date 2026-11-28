<?php

namespace App\Console\Commands;

use App\Models\PeriodePo;
use App\Models\Pesanan;
use App\Models\Produk;
use App\Models\TarifOngkir;
use App\Services\CreateOrderService;
use Illuminate\Console\Command;

/**
 * Membuat satu pesanan contoh untuk menguji halaman bayar dan cek status.
 * HANYA untuk lingkungan lokal (menolak jalan di server).
 *
 * Contoh:
 *   php artisan pesanan:contoh
 *   php artisan pesanan:contoh --bayar=cod --terima=antar
 *   php artisan pesanan:contoh --status=ditolak
 */
class BuatPesananContoh extends Command
{
    protected $signature = 'pesanan:contoh
        {--bayar=non_tunai : non_tunai atau cod}
        {--terima=ambil : ambil atau antar}
        {--status=baru : baru, menunggu, lunas, ditolak, atau dibatalkan}';

    protected $description = 'Buat satu pesanan contoh (hanya lokal) untuk menguji halaman bayar dan cek status';

    public function handle(CreateOrderService $service): int
    {
        if (! app()->environment('local')) {
            $this->error('Perintah ini hanya boleh dijalankan di lingkungan lokal.');

            return self::FAILURE;
        }

        $bayar = (string) $this->option('bayar');
        $terima = (string) $this->option('terima');
        $status = (string) $this->option('status');

        if (! in_array($bayar, ['non_tunai', 'cod'], true)
            || ! in_array($terima, ['ambil', 'antar'], true)
            || ! in_array($status, ['baru', 'menunggu', 'lunas', 'ditolak', 'dibatalkan'], true)) {
            $this->error('Pilihan tidak valid. --bayar: non_tunai|cod, --terima: ambil|antar, --status: baru|menunggu|lunas|ditolak|dibatalkan');

            return self::FAILURE;
        }

        $po = PeriodePo::dibuka()->orderBy('tanggal_buka')->first();

        if (! $po) {
            $po = PeriodePo::create([
                'nama_periode' => 'PO Contoh (otomatis)',
                'tanggal_buka' => today(),
                'tanggal_tutup' => today()->addDays(5),
                'tanggal_terima' => today()->addDays(7),
                'status' => PeriodePo::STATUS_AKTIF,
            ]);
            $this->info('Tidak ada PO yang sedang dibuka, dibuat periode contoh baru.');
        }

        $produk = Produk::aktif()->where('min_order', 1)->orderBy('id_produk')->first();

        if (! $produk) {
            $this->error('Belum ada produk aktif. Jalankan dulu: php artisan migrate:fresh --seed');

            return self::FAILURE;
        }

        $data = [
            'nama_pelanggan' => 'Pelanggan Contoh',
            'no_hp' => '081234567890',
            'metode_terima' => $terima,
            'jadwal_terima' => $po->tanggal_terima->format('Y-m-d').' 14:00',
            'metode_bayar' => $bayar,
            'items' => [['id_produk' => $produk->id_produk, 'jumlah' => 1]],
        ];

        if ($terima === Pesanan::METODE_ANTAR) {
            $tarif = TarifOngkir::orderBy('biaya')->first();

            if (! $tarif) {
                $this->error('Belum ada tarif ongkir. Jalankan dulu: php artisan migrate:fresh --seed');

                return self::FAILURE;
            }

            $data['alamat'] = 'Jl. Contoh No. 1, RT 01 RW 01, dekat masjid';
            $data['id_tarif'] = $tarif->id_tarif;
        }

        $pesanan = $service->buat($data);

        match ($status) {
            'menunggu' => $pesanan->update(['status_bayar' => Pesanan::BAYAR_MENUNGGU]),
            'lunas' => $pesanan->update([
                'status_bayar' => Pesanan::BAYAR_LUNAS,
                'status_pesanan' => Pesanan::PESANAN_DIPROSES,
            ]),
            'ditolak' => $pesanan->update([
                'status_bayar' => Pesanan::BAYAR_DITOLAK,
                'alasan_tolak' => 'Nominal tidak sesuai',
            ]),
            'dibatalkan' => $pesanan->update([
                'status_pesanan' => Pesanan::PESANAN_DIBATALKAN,
                'alasan_batal' => 'Batas waktu pembayaran terlewati',
            ]),
            default => null,
        };

        $this->info('Pesanan contoh dibuat.');
        $this->line('KODE PESANAN: '.$pesanan->kode_pesanan);
        $this->line('Status      : /pesanan/'.$pesanan->kode_pesanan);
        $this->line('Bayar       : /pesanan/'.$pesanan->kode_pesanan.'/bayar');
        $this->line('Atau buka /cek-status lalu masukkan kode di atas.');

        return self::SUCCESS;
    }
}
