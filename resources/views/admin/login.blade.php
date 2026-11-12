<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Admin | {{ config('app.name') }}</title>
    {{-- Tampilan sementara dari Backend. Frontend (Elda) boleh mengganti seluruh tampilan,
         asalkan nama field (username, password), @csrf, dan route('admin.login.submit') dipertahankan. --}}
    <style>
        body { font-family: system-ui, sans-serif; background: #f5f5f4; margin: 0; display: grid; place-items: center; min-height: 100vh; }
        main { background: #fff; padding: 2rem; border-radius: 12px; width: min(360px, 90vw); box-shadow: 0 2px 12px rgba(0,0,0,.08); }
        h1 { font-size: 1.25rem; margin: 0 0 1rem; }
        label { display: block; font-size: .875rem; margin: .75rem 0 .25rem; }
        input { width: 100%; box-sizing: border-box; padding: .6rem; border: 1px solid #ccc; border-radius: 8px; }
        button { margin-top: 1.25rem; width: 100%; padding: .7rem; border: 0; border-radius: 8px; background: #1c1917; color: #fff; cursor: pointer; }
        .error { background: #fee2e2; color: #991b1b; padding: .6rem; border-radius: 8px; font-size: .875rem; }
    </style>
</head>
<body>
<main>
    <h1>Login Admin</h1>

    @if ($errors->any())
        <p class="error">{{ $errors->first() }}</p>
    @endif

    <form method="POST" action="{{ route('admin.login.submit') }}">
        @csrf
        <label for="username">Username</label>
        <input id="username" name="username" type="text" value="{{ old('username') }}"
               required autofocus autocomplete="username">

        <label for="password">Password</label>
        <input id="password" name="password" type="password"
               required autocomplete="current-password">

        <button type="submit">Masuk</button>
    </form>
</main>
</body>
</html>
