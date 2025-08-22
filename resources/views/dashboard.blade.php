@extends('layouts.app')

{{-- ▼▼▼ SEMUA CSS KHUSUS HALAMAN INI MASUK KE SLOT 'styles' ▼▼▼ --}}
@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css"/>
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css"/>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;700&display=swap" rel="stylesheet">

<style>
    #map { 
        height: 60vh; 
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    .filter-card, .map-card {
        margin-bottom: 1.5rem;
    }
    .legend {
        padding: 6px 8px; font: 14px/16px Arial, Helvetica, sans-serif;
        background: rgba(255,255,255,0.8);
        box-shadow: 0 0 15px rgba(0,0,0,0.2); border-radius: 5px;
    }
    .legend i {
        width: 18px; height: 18px; float: left;
        margin-right: 8px; opacity: 0.9; border-radius: 50%;
    }
    body {
        font-family: 'Quicksand', sans-serif;
    }
</style>
@endsection


{{-- ▼▼▼ KONTEN UTAMA HALAMAN (HTML) MASUK KE SLOT 'content' ▼▼▼ --}}
@section('content')
<div class="container">
    {{-- FORM FILTER --}}
    <div class="card shadow-sm filter-card">
        <div class="card-body">
            <form action="{{ route('dashboard') }}" method="GET">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label for="sektor_usaha" class="form-label">Sektor Usaha</label>
                        <select name="sektor_usaha" id="sektor_usaha" class="form-select">
                            <option value="">Semua Sektor</option>
                            @foreach($sectors as $sector)
                                <option value="{{ $sector }}" {{ request('sektor_usaha') == $sector ? 'selected' : '' }}>{{ $sector }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="kecamatan_id" class="form-label">Kecamatan</label>
                        <select name="kecamatan_id" id="kecamatan_id_filter" class="form-select">
                            <option value="">Semua Kecamatan</option>
                             @foreach($kecamatans as $kecamatan)
                                <option value="{{ $kecamatan->id }}" {{ request('kecamatan_id') == $kecamatan->id ? 'selected' : '' }}>{{ $kecamatan->nama_kecamatan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="kelurahan_id" class="form-label">Kelurahan</label>
                        <select name="kelurahan_id" id="kelurahan_id_filter" class="form-select">
                            <option value="">Semua Kelurahan</option>
                        </select>
                    </div>
                     <div class="col-md-3">
                        <label for="status_legalitas" class="form-label">Status NIB</label>
                        <select name="status_legalitas" id="status_legalitas" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="legal" {{ request('status_legalitas') == 'legal' ? 'selected' : '' }}>Dengan NIB</option>
                            <option value="illegal" {{ request('status_legalitas') == 'illegal' ? 'selected' : '' }}>Tanpa NIB</option>
                        </select>
                    </div>
                    <div class="col-md-12 text-end">
                        <a href="{{ route('dashboard') }}" class="btn btn-secondary">Reset Filter</a>
                        <button type="submit" class="btn btn-primary">Terapkan Filter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- PETA --}}
    <div class="card shadow-sm map-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0 fw-bold">Peta Persebaran UMKM</h4>
            <div>
                <button id="find-me" class="btn btn-info btn-sm"><i class="bi bi-geo"></i> Cari Lokasi Saya</button>
                <div class="btn-group btn-group-sm" role="group">
                    <button id="view-standard" type="button" class="btn btn-outline-primary">Standar</button>
                    <button id="view-cluster" type="button" class="btn btn-primary active">Cluster</button>
                    <button id="view-heatmap" type="button" class="btn btn-outline-primary">Heatmap</button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div id="map"></div>
        </div>
    </div>

    {{-- GRAFIK & PROGRAM --}}
    <div class="row">
        <div class="col-md-6 mt-4">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h4 class="mb-0 fw-bold">Grafik Pertumbuhan UMKM (12 Bulan Terakhir)</h4>
                </div>
                <div class="card-body">
                    <canvas id="growthChart" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6 mt-4">
            <div class="card shadow-sm">
                <div class="card-header fw-bold">Program Pembinaan Terpopuler</div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                    @foreach($programData as $program)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ $program->nama_program }}
                            <span class="badge bg-primary rounded-pill">{{ $program->pesertas_count }} Peserta</span>
                        </li>
                    @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


{{-- ▼▼▼ SEMUA JAVASCRIPT KHUSUS HALAMAN INI MASUK KE SLOT 'scripts' ▼▼▼ --}}
@section('scripts')
{{-- JS Leaflet --}}
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>
<script src="https://unpkg.com/leaflet.heat@0.2.0/dist/leaflet-heat.js"></script>
{{-- JS Chart --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // --- Letakkan semua kode JS peta & grafikmu di sini ---
    // Contoh inisialisasi peta sederhana:
    var map = L.map('map').setView([-7.8225, 112.0118], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap'
    }).addTo(map);

    var locations = @json($locations);
    locations.forEach(function(location) {
        if (location.latitude && location.longitude) {
            L.marker([location.latitude, location.longitude]).addTo(map)
                .bindPopup(`<b>${location.nama_usaha}</b>`);
        }
    });

    // Kode untuk grafik
    const ctx = document.getElementById('growthChart');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($chartLabels),
            datasets: [{
                label: 'Jumlah UMKM Baru',
                data: @json($chartValues),
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.1
            }]
        }
    });
});
</script>
@endsection
