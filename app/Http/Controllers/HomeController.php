<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $kategoriList = ['Semua', 'Cakes', 'Cookies', 'Brownies', 'Donuts', 'Pie'];
        $kategori = $request->query('kategori', 'Semua');

        $produk = collect([
            ['nama' => 'Mocca Jadul', 'deskripsi' => 'Kue bolu atau sponge cake klasik dengan aroma dan rasa khas moka', 'harga' => 80000, 'kategori' => 'Cakes', 'gambar' => 'images/produk/mocca-jadul.jpeg'],
            ['nama' => 'Strawberry Shortcake', 'deskripsi' => 'Dessert klasik terbuat dari kombinasi kue lembut, whipped cream, dan potongan buah stroberi', 'harga' => 180000, 'kategori' => 'Cakes', 'gambar' => 'images/produk/strawberry-shortcake.jpeg'],
            ['nama' => 'Classic Brownies', 'deskripsi' => 'Kue coklat panggang yang memiliki tekstur padat, lembut (fudgy), dan lumer saat digigit', 'harga' => 180000, 'kategori' => 'Brownies', 'gambar' => 'images/produk/classic-brownies.jpeg'],
            ['nama' => 'Mango Sticky Cake', 'deskripsi' => 'Vanila cake dengan filling selai mangga, mochi dan whipping cream.', 'harga' => 190000, 'kategori' => 'Cakes', 'gambar' => 'images/produk/mango-sticky-cake.jpg'],
            ['nama' => 'Quiche Lorraine', 'deskripsi' => 'Kue tart atau pie gurih khas Perancis dari wilayah Lorraine', 'harga' => 190000, 'kategori' => 'Pie', 'gambar' => 'images/produk/quiche-lorraine.jpg'],
            ['nama' => 'Martabak Cake', 'deskripsi' => 'Butter cake dengan gurihnya nougat dan meses coklat.', 'harga' => 200000, 'kategori' => 'Cakes', 'gambar' => 'images/produk/martabak-cake.jpg'],
        ]);

        if ($kategori !== 'Semua') {
            $produk = $produk->where('kategori', $kategori);
        }

        $po = [
            'nama' => 'PO Akhir Tahun',
            'buka' => '9 Nov 2026',
            'tutup' => '22 Nov 2026',
            'diterima' => '27 Nov 2026',
        ];

        return view('home', compact('kategoriList', 'kategori', 'produk', 'po'));
    }
}