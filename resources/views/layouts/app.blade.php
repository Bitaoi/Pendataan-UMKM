<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendataan UMKM</title>
    </head>
<body>
    <header>
        <nav>
            <a href="{{ route('dashboard') }}">Dashboard</a> |
            <a href="{{ route('umkm.index') }}">Data UMKM</a> |
            <a href="{{ route('peta.index') }}">Peta Persebaran</a> |

            <form method="POST" action="{{ route('logout') }}>
                @csrf
                <button type="submit">Logout</button>
            </form> 
        </nav>
    </header>

    <main>
        @yield('content') </main>

    <footer>
        <p>&copy; 2025 Proyek Bita</p>
    </footer>
    @stack('scripts') </body>
</html>