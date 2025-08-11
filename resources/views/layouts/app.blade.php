<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>Pendataan UMKM</title>

    <link rel="preconnect" href="https://fonts.bunny.net" />
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    {{-- Load CSS dan JS dengan Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100 dark:bg-gray-900 min-h-screen">

    <div class="flex flex-col min-h-screen">

        {{-- Navigasi --}}
        @include('layouts.navigation')

        {{-- Header Halaman (opsional) --}}
        @if (isset($header))
            <header class="bg-white dark:bg-gray-800 shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        {{-- Menu Navigasi Tambahan --}}
        <nav class="bg-white dark:bg-gray-800 shadow py-2 px-4">
            <a href="{{ route('dashboard') }}" class="mr-4 hover:underline">Dashboard</a>
            <a href="{{ route('umkm.index') }}" class="mr-4 hover:underline">Data UMKM</a>
            <a href="{{ route('peta.index') }}" class="mr-4 hover:underline">Peta Persebaran</a>

            <form method="POST" action="{{ route('logout') }}" class="inline ml-4">
                @csrf
                <button type="submit" class="text-red-600 hover:underline">Logout</button>
            </form>
        </nav>

        {{-- Konten Halaman --}}
        <main class="flex-grow py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @yield('content')
        </main>

        {{-- Footer --}}
        <footer class="bg-white dark:bg-gray-800 shadow py-4 text-center text-sm text-gray-500">
            <p>&copy; Data Persebaran UMKM 2025</p>
        </footer>

    </div>

    @stack('scripts')
</body>
</html>
