<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Admin | {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen grid place-items-center px-5">
    <main class="w-full max-w-sm bg-card border border-brand p-8">
        <img src="{{ asset('images/logo.png') }}" alt="" class="w-16 h-16 rounded-full mx-auto">
        <h1 class="font-display font-bold text-2xl text-center uppercase mt-3 mb-6">Login Admin</h1>

        @if ($errors->any())
            <p class="bg-[#fde8e8] border border-red-400 text-red-700 px-3 py-2 mb-4 text-sm">
                {{ $errors->first() }}
            </p>
        @endif

        <form method="POST" action="{{ route('admin.login.submit') }}">
            @csrf
            <label for="username" class="block text-sm font-semibold mb-1">Username</label>
            <input id="username" name="username" type="text" value="{{ old('username') }}"
                   required autofocus autocomplete="username"
                   class="w-full border border-sand bg-white px-3 py-2.5 mb-4">

            <label for="password" class="block text-sm font-semibold mb-1">Password</label>
            <input id="password" name="password" type="password"
                   required autocomplete="current-password"
                   class="w-full border border-sand bg-white px-3 py-2.5 mb-6">

            <button type="submit" class="btn w-full py-3">Masuk</button>
        </form>
    </main>
</body>
</html>