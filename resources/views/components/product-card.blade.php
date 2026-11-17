@props([
    'nama',
    'harga',
    'deskripsi' => null,
    'gambar' => null,
    'kategori' => null,
    'satuan' => null,
    'url' => null,
])

<div class="border border-brand bg-card text-center flex flex-col">
    <a href="{{ $url ?? '#' }}">
        @if ($gambar)
            <img src="{{ asset($gambar) }}" alt="{{ $nama }}" class="w-full h-[210px] object-cover">
        @else
            <div class="w-full h-[210px] bg-sand grid place-items-center text-muted text-sm">Foto belum tersedia</div>
        @endif
    </a>
    <h3 class="mt-3.5 mb-1 text-lg font-bold">
        <a href="{{ $url ?? '#' }}">{{ $nama }}</a>
    </h3>
    <p class="text-muted text-[13px] mx-4 flex-1">{{ $deskripsi }}</p>
    <b class="text-brand text-lg my-3.5">
        Rp {{ number_format($harga, 0, ',', '.') }}
        @if ($satuan)
            <span class="text-muted text-xs font-normal">/ {{ $satuan }}</span>
        @endif
    </b>
    <a href="#" class="btn w-full py-3.5">Pesan</a>
</div>