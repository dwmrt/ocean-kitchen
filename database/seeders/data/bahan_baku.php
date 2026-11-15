<?php

// Format: nama_bahan => [satuan, jumlah, harga_bahan, harga_estimasi]
// harga_bahan = harga untuk sejumlah 'jumlah' satuan (contoh: 1000 gram seharga 13000).
// harga_estimasi = true berarti harga atau ukuran kemasan masih perkiraan/asumsi
// dan perlu dikonfirmasi ke pemilik toko.

return [
    'Air' => ['ml', 1000, 0, false], // gratis
    'Air es' => ['ml', 1000, 0, false], // gratis
    'Butter' => ['gram', 200, 9000, false],
    'Gula pasir' => ['gram', 1000, 17500, false],
    'Keju cheddar' => ['gram', 2000, 94000, false],
    'Ragi instan' => ['gram', 500, 51000, false],
    'Smoked beef' => ['gram', 500, 63000, false],
    'Tepung ketan' => ['gram', 500, 34000, false],
    'Tepung maizena' => ['gram', 500, 10500, false],
    'Tepung terigu' => ['gram', 1000, 13000, false],
    'Whipping cream' => ['ml', 1000, 63000, false],
    'Baking powder' => ['gram', 100, 7500, true], // asumsi kemasan 100 gram
    'Baking soda' => ['gram', 100, 7500, true], // asumsi kemasan 100 gram
    'Bawang bombay' => ['gram', 1000, 35000, true], // perkiraan
    'Brown sugar' => ['gram', 1000, 25000, true], // perkiraan
    'Coklat bubuk' => ['gram', 1000, 110000, true], // asumsi per kg
    'Dark chocolate' => ['gram', 1000, 77000, true], // asumsi per kg
    'Emulsifier (SP)' => ['gram', 100, 15000, true], // perkiraan
    'Filling (selai/krim/coklat)' => ['gram', 1000, 50000, true], // asumsi per kg
    'Garam' => ['gram', 1000, 10000, true], // perkiraan
    'Gula halus' => ['gram', 1000, 20000, true], // perkiraan
    'Gula palem' => ['gram', 1000, 40000, true], // perkiraan
    'Jagung manis' => ['gram', 450, 8000, true], // asumsi 3 buah = 450 gram pipilan
    'Keju edam' => ['gram', 1000, 130000, true], // perkiraan
    'Kuning telur' => ['butir', 16, 25000, true], // perkiraan, sama dengan telur
    'Mangga matang' => ['gram', 1000, 25000, true], // perkiraan
    'Merica' => ['gram', 1000, 120000, true], // perkiraan
    'Meses coklat' => ['gram', 1000, 70000, true], // asumsi per kg
    'Minyak goreng' => ['ml', 1000, 21000, true], // asumsi per liter
    'Nougat' => ['gram', 1000, 100000, true], // perkiraan
    'Pala bubuk' => ['gram', 1000, 200000, true], // perkiraan
    'Pasta moka' => ['gram', 1000, 120000, true], // perkiraan
    'Pisang matang' => ['gram', 1000, 15000, true], // perkiraan
    'Santan' => ['ml', 1000, 40000, true], // perkiraan
    'Selai stroberi' => ['gram', 1000, 50000, true], // perkiraan
    'Stroberi segar' => ['gram', 250, 15000, true], // asumsi 1 mika = 250 gram
    'Susu bubuk' => ['gram', 1000, 70000, true], // asumsi per kg
    'Susu cair' => ['ml', 1000, 21000, true], // asumsi per liter
    'Susu kental manis' => ['gram', 1000, 45000, true], // asumsi per kg
    'Telur' => ['butir', 16, 25000, true], // asumsi Rp25.000 per kg, 1 kg = 16 butir
    'Vanili' => ['ml', 100, 31000, true], // asumsi kemasan 100 ml
];
