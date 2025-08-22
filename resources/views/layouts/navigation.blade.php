<nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container-fluid">
        <a class="navbar-brand text-lemon-lime fw-bold" href="{{ route('dashboard') }}">SIPPKM Dashboard</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    {{-- Menambahkan kelas 'active' jika rute saat ini adalah 'dashboard' --}}
                    <a class="nav-link text-anti-flesh-white {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Dashboard</a>
                </li>
                <li class="nav-item">
                    {{-- Menambahkan kelas 'active' jika rute saat ini adalah 'umkm.index', 'umkm.create', dll. --}}
                    <a class="nav-link text-anti-flesh-white {{ request()->routeIs('umkm.*') ? 'active' : '' }}" href="{{ route('umkm.index') }}">Data UMKM</a>
                </li>
                <li class="nav-item">
                    {{-- Menambahkan kelas 'active' jika rute saat ini adalah 'programs.index', 'programs.show', dll. --}}
                    <a class="nav-link text-anti-flesh-white {{ request()->routeIs('programs.*') ? 'active' : '' }}" href="{{ route('programs.index') }}">Program Pembinaan</a>
                </li>
            </ul>
            <form method="POST" action="{{ route('logout') }}" class="d-flex">
                @csrf
                <button type="submit" class="btn btn-lemon-lime text-dark-green fw-bold">Logout</button>
            </form>
        </div>
    </div>
</nav>
