<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>Cek status pesanan | {{ config('app.name') }}</title>
    {{-- Tampilan sementara dari Backend. Frontend (Elda) boleh mengganti seluruhnya.
         Form: POST route('pesanan.cari'), field "kode", @csrf. Error ada di $errors->first('kode'). --}}
    <style>
        body { font-family: system-ui, sans-serif; margin: 2rem auto; max-width: 480px; padding: 0 1rem; }
        input { width: 100%; box-sizing: border-box; padding: .6rem; border: 1px solid #ccc; border-radius: 8px; text-transform: uppercase; }
        button { margin-top: 1rem; padding: .7rem 1.2rem; border: 0; border-radius: 8px; background: #1c1917; color: #fff; cursor: pointer; }
        .error { background: #fee2e2; color: #991b1b; padding: .6rem; border-radius: 8px; }
    </style>
</head>
<body>
    <h1>Cek status pesanan</h1>
    <p>Masukkan kode pesanan yang Anda terima setelah memesan (contoh: OK-7K2M9QXA).</p>

    @if ($errors->has('kode'))
        <p class="error">{{ $errors->first('kode') }}</p>
    @endif

    <form method="POST" action="{{ route('pesanan.cari') }}">
        @csrf
        <label for="kode">Kode pesanan</label>
        <input id="kode" name="kode" type="text" value="{{ old('kode') }}" placeholder="OK-XXXXXXXX"
               required autofocus autocomplete="off">
        <button type="submit">Cek status</button>
    </form>
</body>
</html>
