<nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container-fluid">
        <a class="navbar-brand text-lemon-lime fw-bold" href="{{ route('dashboard') }}">SIPPKM Dashboard</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link text-anti-flesh-white" href="{{ route('dashboard') }}">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-anti-flesh-white" href="{{ route('umkm.index') }}">Data UMKM</a>
                </li>
            </ul>
            <form method="POST" action="{{ route('logout') }}" class="d-flex">
                @csrf
                <button type="submit" class="btn btn-lemon-lime text-dark-green fw-bold">Logout</button>
            </form>
        </div>
    </div>
</nav>
