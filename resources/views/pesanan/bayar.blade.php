<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pesanan {{ $pesanan->kode_pesanan }} | {{ config('app.name') }}</title>
    {{-- Tampilan sementara dari Backend. Frontend (Elda) boleh mengganti seluruhnya dan memakai
         layout yang sama dengan halaman lain. Data: $pesanan (+ detail.produk), $qrisUrl, $bank, $alamatAmbil.
         Form unggah: POST route('pesanan.bukti', kode), field "bukti_bayar" (JPG/PNG, maks 5 MB),
         enctype multipart/form-data + @csrf. Pratinjau gambar (JavaScript) menjadi tugas Frontend. --}}
    <style>
        body { font-family: system-ui, sans-serif; margin: 2rem auto; max-width: 720px; padding: 0 1rem; }
        table { width: 100%; border-collapse: collapse; margin: 1rem 0; }
        th, td { text-align: left; padding: .4rem; border-bottom: 1px solid #ddd; }
        td.angka, th.angka { text-align: right; }
        .kode { font-size: 1.5rem; font-weight: 700; letter-spacing: .05em; }
        .total { font-size: 1.5rem; font-weight: 700; }
        .ok { background: #dcfce7; color: #166534; padding: .6rem; border-radius: 8px; }
        .error { background: #fee2e2; color: #991b1b; padding: .6rem; border-radius: 8px; }
        img { max-width: 280px; }
    </style>
</head>
<body>
    @if (session('status'))
        <p class="ok">{{ session('status') }}</p>
    @endif

    <p>Simpan kode ini untuk mengecek status pesanan:</p>
    <p class="kode">{{ $pesanan->kode_pesanan }}</p>

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
                <td colspan="3">Ongkir ({{ $pesanan->metode_terima === 'antar' ? 'antar' : 'ambil di rumah' }})</td>
                <td class="angka">{{ number_format($pesanan->ongkir, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <p>Total: <span class="total">Rp {{ number_format($pesanan->total_bayar, 0, ',', '.') }}</span></p>
    <p>Jadwal terima: {{ $pesanan->jadwal_terima->translatedFormat('l, j F Y, H.i') }} WIB</p>

    @if ($pesanan->metode_terima === 'ambil' && $alamatAmbil)
        <p>Alamat pengambilan: {{ $alamatAmbil }}</p>
    @endif

    @if ($pesanan->status_pesanan === 'dibatalkan')
        <h2>Pesanan dibatalkan</h2>
        <p>{{ $pesanan->alasan_batal ?: 'Pesanan ini telah dibatalkan.' }}</p>

    @elseif ($pesanan->metode_bayar === 'non_tunai')
        <h2>Pembayaran (QRIS atau transfer)</h2>

        @if ($pesanan->status_bayar === 'lunas')
            <p class="ok">Pembayaran sudah diterima (lunas). Terima kasih!</p>

        @elseif ($pesanan->status_bayar === 'menunggu_verifikasi')
            <p class="ok">Bukti pembayaran sudah diterima dan sedang diverifikasi admin.</p>

        @else
            @if ($pesanan->status_bayar === 'ditolak')
                <p class="error">Bukti pembayaran ditolak: {{ $pesanan->alasan_tolak }}. Silakan unggah ulang.</p>
            @endif

            <p>Bayar tepat <strong>Rp {{ number_format($pesanan->total_bayar, 0, ',', '.') }}</strong>
               sebelum {{ $pesanan->batas_bayar->translatedFormat('j F Y, H.i') }} WIB.
               Pesanan yang belum dibayar sampai batas itu dibatalkan otomatis.</p>

            @if ($qrisUrl)
                <p>Scan QRIS:</p>
                <img src="{{ $qrisUrl }}" alt="QRIS Ocean Kitchen">
            @endif

            @if (! empty($bank['nomor']))
                <p>Atau transfer ke {{ $bank['nama'] }}: <strong>{{ $bank['nomor'] }}</strong>
                   a.n. {{ $bank['atas_nama'] }}</p>
            @endif

            <h3>Unggah bukti pembayaran</h3>
            @if ($errors->has('bukti_bayar'))
                <p class="error">{{ $errors->first('bukti_bayar') }}</p>
            @endif
            <form method="POST" action="{{ route('pesanan.bukti', $pesanan->kode_pesanan) }}" enctype="multipart/form-data">
                @csrf
                <input type="file" name="bukti_bayar" accept="image/jpeg,image/png" required>
                <button type="submit">Unggah bukti</button>
            </form>
            <p><small>Format JPG atau PNG, maksimal 5 MB.</small></p>
        @endif

    @else
        <h2>Bayar di tempat (COD)</h2>
        <p>Siapkan <strong>Rp {{ number_format($pesanan->total_bayar, 0, ',', '.') }}</strong>
           untuk dibayar saat pesanan {{ $pesanan->metode_terima === 'antar' ? 'diantar' : 'diambil' }}.</p>
    @endif
</body>
</html>
