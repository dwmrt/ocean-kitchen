@extends('layouts.app')
@section('title', 'Katalog - Ocean Kitchen')

@section('content')
    <x-po-banner :po="$po" />

    <h2 id="katalog" class="text-[26px] font-bold mt-[30px] mb-3.5">Katalog Menu</h2>

    <x-category-tabs :list="$kategoriList" :aktif="$kategori" route="katalog" />

    <div class="grid grid-cols-[repeat(auto-fill,minmax(270px,1fr))] gap-6 mt-6">
        @forelse ($produk as $p)
            <x-product-card
                :nama="$p['nama']"
                :deskripsi="$p['deskripsi']"
                :harga="$p['harga']"
                :gambar="$p['gambar']"
                :kategori="$p['kategori']" />
        @empty
            <p class="col-span-full text-center text-muted py-10">Belum ada produk di kategori ini.</p>
        @endforelse
    </div>

    <h2 id="tarif-ongkir" class="text-[26px] font-bold mt-9 mb-3.5">Tarif Ongkos Kirim</h2>

    <div class="overflow-x-auto">
        <table class="w-full border border-brand bg-card text-left">
            <thead class="bg-brand text-white">
                <tr>
                    <th class="px-4 py-3">Wilayah</th>
                    <th class="px-4 py-3">Biaya</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($ongkir as $o)
                    <tr class="border-t border-sand">
                        <td class="px-4 py-3">{{ $o['wilayah'] }}</td>
                        <td class="px-4 py-3 font-semibold">Rp {{ number_format($o['biaya'], 0, ',', '.') }}</td>
                    </tr>
                @endforeach
                <tr class="border-t border-sand">
                    <td class="px-4 py-3">Ambil di toko</td>
                    <td class="px-4 py-3 font-semibold">Gratis</td>
                </tr>
            </tbody>
        </table>
    </div>
@endsection