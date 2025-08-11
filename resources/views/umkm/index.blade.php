@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="card-title mb-0 fw-bold">Data UMKM</h4>
                <a href="{{ route('umkm.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle-fill me-2"></i>Tambah Data
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @forelse ($umkms as $umkm)
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-3">
                                <h6 class="mb-1">{{ $umkm->nama_usaha }}</h6>
                                <span class="badge bg-secondary">{{ $umkm->sektor_usaha }}</span>
                            </div>
                            <div class="col-md-5">
                                <p class="mb-1"><strong>Pemilik:</strong> {{ $umkm->nama_pemilik }}</p>
                                <p class="mb-0 text-muted"><i class="bi bi-geo-alt-fill me-1"></i>{{ $umkm->alamat }}, Kel. {{ $umkm->kelurahan->nama_kelurahan ?? 'N/A' }}, Kec. {{ $umkm->kecamatan->nama_kecamatan ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                <a href="{{ route('umkm.edit', $umkm->id) }}" class="btn btn-warning btn-sm me-2">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </a>
                                <form action="{{ route('umkm.destroy', $umkm->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="bi bi-trash-fill"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="alert alert-info text-center">
                    Belum ada data UMKM yang diinput. Silakan klik tombol "Tambah Data".
                </div>
            @endforelse

            <!-- Pagination Links -->
            <div class="mt-4 d-flex justify-content-center">
                {{ $umkms->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
