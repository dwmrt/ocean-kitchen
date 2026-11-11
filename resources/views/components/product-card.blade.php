@props(['nama', 'deskripsi', 'harga', 'gambar', 'kategori' => null])

<div class="border border-brand bg-card text-center flex flex-col">
    <img src="{{ asset($gambar) }}" alt="{{ $nama }}" class="w-full h-[210px] object-cover">
    <h3 class="mt-3.5 mb-1 text-lg font-bold">{{ $nama }}</h3>
    <p class="text-muted text-[13px] mx-4 flex-1">{{ $deskripsi }}</p>
    <b class="text-brand text-lg my-3.5">Rp {{ number_format($harga, 0, ',', '.') }}</b>
    <a href="#" class="btn w-full py-3.5">Pesan</a>
</div>