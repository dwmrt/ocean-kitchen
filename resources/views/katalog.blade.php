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
                :gambar="$p['gambar'] ?? null"
                :kategori="$p['kategori']"
                :satuan="$p['satuan'] ?? null"
                :ukuran="$p['ukuran'] ?? null"
                :min-order="$p['min_order'] ?? null"
                :url="$p['url_detail'] ?? null"
                :po-dibuka="$po['dibuka'] ?? true" />
        @empty
            <p class="col-span-full text-center text-muted py-10">Belum ada produk di kategori ini.</p>
        @endforelse
    </div>

    <h2 id="tarif-ongkir" class="text-[26px] font-bold mt-9 mb-3.5">Tarif Ongkos Kirim</h2>
    <x-ongkir-table :ongkir="$ongkir" />
@endsection