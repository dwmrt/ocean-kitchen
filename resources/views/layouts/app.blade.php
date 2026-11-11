<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Ocean Kitchen')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex flex-col">
    @include('partials.navbar')
    <main class="flex-1 w-full max-w-[1180px] mx-auto px-5 py-6">
        @yield('content')
    </main>
    @include('partials.footer')
</body>
</html>