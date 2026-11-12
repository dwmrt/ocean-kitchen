<?php

namespace App\Http\Controllers;

use App\Support\DataDummy;
use Illuminate\Http\Request;

class KatalogController extends Controller
{
    public function index(Request $request)
    {
        $kategori = $request->query('kategori', 'Semua');
        $produk = DataDummy::produk();
        if ($kategori !== 'Semua') {
            $produk = $produk->where('kategori', $kategori);
        }

        return view('katalog', [
            'kategoriList' => DataDummy::kategori(),
            'kategori' => $kategori,
            'produk' => $produk,
            'po' => DataDummy::po(),
            'ongkir' => DataDummy::ongkir(),
        ]);
    }
}