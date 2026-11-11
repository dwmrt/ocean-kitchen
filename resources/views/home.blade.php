@extends('layouts.app')
@section('title', 'Beranda - Ocean Kitchen')

@section('content')
    {{-- Banner PO --}}
    <div class="bg-[#efdbce] border border-brand px-6 py-5">
        <b class="bg-white text-brand px-2 py-1 mr-3">■ PO DIBUKA</b>
        Periode Pre-Order: <b>{{ $po['buka'] }} - {{ $po['tutup'] }}</b>
        · Pesanan diterima {{ $po['diterima'] }}
    </div>

    {{-- Katalog --}}
    <h2 id="katalog" class="text-[26px] font-bold mt-[30px] mb-3.5">Katalog Menu</h2>

    <div class="flex overflow-x-auto border-y border-ink">
        @foreach ($kategoriList as $k)
            <a href="{{ route('home', ['kategori' => $k]) }}#katalog"
               class="flex-1 text-center py-3 whitespace-nowrap {{ $kategori === $k ? 'text-brand font-semibold border-b-2 border-brand' : 'text-muted' }}">
                {{ $k }}
            </a>
        @endforeach
    </div>

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

    {{-- Cara Pesan --}}
    <h2 id="cara-pesan" class="text-[26px] font-bold mt-9 mb-3.5">Cara Pesan</h2>

    <div class="grid grid-cols-[repeat(auto-fit,minmax(250px,1fr))] gap-5">
        @foreach ([
            ['Pilih pesanan', 'Lihat katalog dan pilih pesanan selama periode PO dibuka.'],
            ['Isi data & jadwal', 'Lengkapi data diri, pilih ambil di toko atau antar, lalu pilih tanggal.'],
            ['Bayar', 'Transfer sesuai total, lalu kirim bukti bayar untuk diverifikasi admin.'],
        ] as $i => $langkah)
            <div class="border border-brand bg-card p-4 flex gap-3.5">
                <i class="bg-brand text-white not-italic w-[34px] h-[34px] grid place-items-center shrink-0">{{ $i + 1 }}</i>
                <div>
                    <b>{{ $langkah[0] }}</b><br>
                    <small class="text-muted">{{ $langkah[1] }}</small>
                </div>
            </div>
        @endforeach
    </div>
@endsection