<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    <!-- Vite (CSS & JS utama dari Laravel) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    {{-- ▼▼▼ SLOT UNTUK CSS TAMBAHAN DARI HALAMAN LAIN ▼▼▼ --}}
    @yield('styles')
</head>
<body>
    <div id="app">
        @include('layouts.navigation')

        <main class="py-4">
            <div class="container">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    {{-- ▼▼▼ SLOT UNTUK JAVASCRIPT TAMBAHAN DARI HALAMAN LAIN ▼▼▼ --}}
    @stack('scripts')
</body>
</html>
