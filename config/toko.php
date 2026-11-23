<?php

/*
| Data toko. Nilainya DIISI DI .env (bukan di sini), karena repo GitHub ini publik dan
| rekening, alamat, dan lokasi dapur tidak boleh ikut ter-commit. Lihat .env.example.
*/

// Angka dari .env; kosong atau tidak diisi menjadi null (bukan 0).
$angka = function (string $kunci): ?float {
    $nilai = env($kunci);

    return ($nilai === null || $nilai === '') ? null : (float) $nilai;
};

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

    // Alamat atau patokan yang BOLEH ditampilkan ke pelanggan yang ambil di rumah.
    'alamat_ambil' => env('TOKO_ALAMAT_AMBIL'),

    // Lokasi dapur untuk hitung ongkir otomatis (Minggu 3). Dipakai HANYA di server,
    // JANGAN dikirim ke browser pelanggan.
    'lokasi' => [
        'lat' => $angka('TOKO_LAT'),
        'lng' => $angka('TOKO_LNG'),
        'jarak_maks_km' => $angka('TOKO_JARAK_MAKS_KM'),
    ],
];
