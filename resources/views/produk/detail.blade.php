<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $produk['nama'] }} | {{ config('app.name') }}</title>
    {{-- Tampilan sementara dari Backend. Frontend (Elda) boleh mengganti seluruhnya
         dan memakai layout yang sama dengan halaman lain. Data yang tersedia ada di $produk dan $po. --}}
    <style>
        body { font-family: system-ui, sans-serif; margin: 2rem auto; max-width: 720px; padding: 0 1rem; }
        img { max-width: 100%; border-radius: 12px; }
        dt { font-weight: 600; margin-top: .75rem; }
        dd { margin: 0; }
    </style>
</head>
<body>
    <p><a href="{{ route('katalog') }}">&larr; Kembali ke katalog</a></p>

    <h1>{{ $produk['nama'] }}</h1>

    @if ($produk['gambar'])
        <img src="{{ asset($produk['gambar']) }}" alt="{{ $produk['nama'] }}">
    @endif

    <p>{{ $produk['deskripsi'] }}</p>

    <dl>
        <dt>Harga</dt>
        <dd>Rp {{ number_format($produk['harga'], 0, ',', '.') }} / {{ $produk['satuan'] }}</dd>

        <dt>Kategori</dt>
        <dd>{{ $produk['kategori'] }}</dd>

        @if ($produk['ukuran'])
            <dt>Ukuran / isi</dt>
            <dd>{{ $produk['ukuran'] }}</dd>
        @endif

        <dt>Minimal order</dt>
        <dd>{{ $produk['min_order'] }} {{ $produk['satuan'] }}</dd>

        @if ($produk['lama_pengerjaan'])
            <dt>Lama pengerjaan</dt>
            <dd>{{ $produk['lama_pengerjaan'] }}</dd>
        @endif

        @if ($produk['daya_tahan'])
            <dt>Daya tahan</dt>
            <dd>{{ $produk['daya_tahan'] }}</dd>
        @endif
    </dl>

    <p>
        @if ($po['dibuka'])
            {{ $po['nama'] }} sedang dibuka sampai {{ $po['tutup'] }}.
        @else
            PO belum dibuka.
        @endif
    </p>
</body>
</html>
