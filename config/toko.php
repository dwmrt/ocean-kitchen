<?php

/*
| Data toko untuk halaman pembayaran.
| Nilainya DIISI DI .env (bukan di sini), karena repo GitHub ini publik dan
| nomor rekening tidak boleh ikut ter-commit. Lihat contoh di .env.example.
*/
return [
    'nama' => env('TOKO_NAMA', 'Ocean Kitchen'),

    'bank' => [
        'nama' => env('TOKO_BANK_NAMA'),
        'nomor' => env('TOKO_BANK_NOREK'),
        'atas_nama' => env('TOKO_BANK_ATAS_NAMA'),
    ],

    // Lokasi gambar QRIS di storage/app/public (juga TIDAK di-commit).
    // Tampilkan dengan: asset('storage/'.config('toko.qris_path'))
    'qris_path' => env('TOKO_QRIS_PATH', 'qris/ocean-kitchen.jpg'),
];
