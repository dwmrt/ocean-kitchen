<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <meta name="referrer" content="no-referrer">
    <title>Status pesanan {{ $pesanan->kode_pesanan }} | {{ config('app.name') }}</title>
    {{-- Tampilan sementara dari Backend. Frontend (Elda) boleh mengganti seluruhnya.
         Data: $pesanan (+ detail.produk), $labelBayar, $labelPesanan, $bisaBayar, $alamatAmbil.
         Halaman ini sengaja TIDAK menampilkan nama, nomor HP, dan alamat pelanggan. --}}
    <style>
        body { font-family: system-ui, sans-serif; margin: 2rem auto; max-width: 720px; padding: 0 1rem; }
        table { width: 100%; border-collapse: collapse; margin: 1rem 0; }
        th, td { text-align: left; padding: .4rem; border-bottom: 1px solid #ddd; }
        td.angka, th.angka { text-align: right; }
        .kode { font-size: 1.5rem; font-weight: 700; letter-spacing: .05em; }
        .status { font-size: 1.25rem; font-weight: 700; }
        .total { font-size: 1.25rem; font-weight: 700; }
        .error { background: #fee2e2; color: #991b1b; padding: .6rem; border-radius: 8px; }
        dt { font-weight: 600; margin-top: .75rem; }
        dd { margin: 0; }
    </style>
</head>
<body>
    <p><a href="{{ route('pesanan.cek') }}">&larr; Cek kode lain</a></p>

    <h1>Status pesanan</h1>
    <p class="kode">{{ $pesanan->kode_pesanan }}</p>
    <p>Dipesan: {{ $pesanan->created_at->translatedFormat('j F Y, H.i') }} WIB</p>

    <dl>
        <dt>Status pesanan</dt>
        <dd class="status">{{ $labelPesanan }}</dd>

        <dt>Status pembayaran</dt>
        <dd>{{ $labelBayar }}</dd>

        <dt>Cara bayar</dt>
        <dd>{{ $pesanan->metode_bayar === 'cod' ? 'Bayar di tempat (COD)' : 'QRIS / transfer bank' }}</dd>

        <dt>Jadwal terima</dt>
        <dd>{{ $pesanan->jadwal_terima->translatedFormat('l, j F Y, H.i') }} WIB</dd>

        <dt>Metode terima</dt>
        <dd>
            @if ($pesanan->metode_terima === 'antar')
                Diantar ke alamat yang Anda isi saat memesan
            @else
                Ambil di rumah{{ $alamatAmbil ? ': ' . $alamatAmbil : '' }}
            @endif
        </dd>
    </dl>

    @if ($pesanan->status_pesanan === 'dibatalkan')
        <p class="error">Pesanan dibatalkan: {{ $pesanan->alasan_batal ?: 'tanpa keterangan.' }}</p>
    @endif

    @if ($pesanan->status_bayar === 'ditolak')
        <p class="error">Bukti pembayaran ditolak: {{ $pesanan->alasan_tolak }}</p>
    @endif

    @if ($bisaBayar)
        @if ($pesanan->batas_bayar)
            <p>Bayar sebelum {{ $pesanan->batas_bayar->translatedFormat('j F Y, H.i') }} WIB.</p>
        @endif
        <p><a href="{{ route('pesanan.bayar', $pesanan->kode_pesanan) }}">Bayar atau unggah bukti pembayaran</a></p>
    @endif

    <table>
        <thead>
            <tr><th>Produk</th><th class="angka">Jumlah</th><th class="angka">Harga</th><th class="angka">Subtotal</th></tr>
        </thead>
        <tbody>
            @foreach ($pesanan->detail as $d)
                <tr>
                    <td>{{ $d->produk->nama_produk }}</td>
                    <td class="angka">{{ $d->jumlah }}</td>
                    <td class="angka">{{ number_format($d->harga_satuan, 0, ',', '.') }}</td>
                    <td class="angka">{{ number_format($d->subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="3">Ongkir</td>
                <td class="angka">{{ number_format($pesanan->ongkir, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <p>Total: <span class="total">Rp {{ number_format($pesanan->total_bayar, 0, ',', '.') }}</span></p>
</body>
</html>
