<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Admin | {{ config('app.name') }}</title>
    <style>
        body { font-family: system-ui, sans-serif; margin: 2rem; }
        button { padding: .5rem 1rem; border: 0; border-radius: 8px; background: #1c1917; color: #fff; cursor: pointer; }
    </style>
</head>
<body>
    {{-- Halaman sementara dari Backend. Frontend akan menggantinya dengan dashboard asli. --}}
    <h1>Dashboard Admin</h1>
    <p>Halo, {{ auth('admin')->user()->nama }}.</p>

    <form method="POST" action="{{ route('admin.logout') }}">
        @csrf
        <button type="submit">Keluar</button>
    </form>
</body>
</html>
