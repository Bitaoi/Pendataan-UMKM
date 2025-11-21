@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css"/>
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css"/>
<link rel="stylesheet" href="https://unpkg.com/leaflet.heat@0.2.0/dist/leaflet-heat.css"/>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;700&display=swap" rel="stylesheet">

<style>
    #map { 
        height: 60vh; 
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        background-color: #f0f0f0; 
    }
    .filter-card, .map-card { margin-bottom: 1.5rem; }
    body { font-family: 'Quicksand', sans-serif; }
    .btn-group .btn.active { background-color: #0d6efd; color: white; }
    .btn-group .btn:not(.active) { background-color: #fff; color: #0d6efd; }
    .map-error-overlay, .chart-error-overlay {
        display: none; color: #721c24; background-color: #f8d7da;
        border: 1px solid #f5c6cb; border-radius: 8px;
        padding: 20px; text-align: center; font-weight: bold;
    }

    /* --- STYLE BARU UNTUK POPUP --- */
    .custom-popup .popup-header {
        font-size: 15px; font-weight: bold; color: #203627;
        border-bottom: 2px solid #EBFF40; padding-bottom: 5px; margin-bottom: 8px;
    }
    .custom-popup table { width: 100%; font-size: 12px; color: #333; }
    .custom-popup td { padding: 2px 0; vertical-align: top; }
    .custom-popup .label-col { width: 70px; font-weight: 600; color: #666; }
    .badge-halal { background-color: #198754; color: white; padding: 1px 5px; border-radius: 4px; font-size: 10px; }
    .badge-non { background-color: #dc3545; color: white; padding: 1px 5px; border-radius: 4px; font-size: 10px; }
    .badge-proses { background-color: #ffc107; color: black; padding: 1px 5px; border-radius: 4px; font-size: 10px; }
</style>
@endsection

@section('content')
{{-- KONTEN HTML (Sama seperti sebelumnya, tidak perlu diubah) --}}
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
                <button id="find-me" class="btn btn-info btn-sm"><i class="bi bi-geo-alt-fill"></i> Lokasi Saya</button>
                <div id="view-toggle" class="btn-group btn-group-sm" role="group">
                    <button id="view-standard" type="button" class="btn btn-outline-primary">Standar</button>
                    <button id="view-cluster" type="button" class="btn btn-primary active">Cluster</button>
                    <button id="view-heatmap" type="button" class="btn btn-outline-primary">Heatmap</button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div id="map-error" class="map-error-overlay"></div>
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
                    <div id="chart-error" class="chart-error-overlay"></div>
                    <canvas id="growthChart" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6 mt-4">
            <div class="card shadow-sm">
                <div class="card-header fw-bold">Program Pembinaan Terpopuler</div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                    @forelse($programData as $program)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ $program->nama_program }}
                            <span class="badge bg-primary rounded-pill">{{ $program->pesertas_count }} Peserta</span>
                        </li>
                    @empty
                        <li class="list-group-item text-center text-muted">Belum ada data program.</li>
                    @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>
<script src="https://unpkg.com/leaflet.heat@0.2.0/dist/leaflet-heat.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // --- Kode Grafik (Tetap Sama) ---
    const chartCanvas = document.getElementById('growthChart');
    try {
        const chartLabels = @json($chartLabels);
        const chartValues = @json($chartValues);
        if (chartLabels && chartValues) {
            new Chart(chartCanvas, {
                type: 'line',
                data: {
                    labels: chartLabels,
                    datasets: [{
                        label: 'Jumlah UMKM Baru',
                        data: chartValues,
                        borderColor: 'rgb(75, 192, 192)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        fill: true,
                        tension: 0.1
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } } }
            });
        }
    } catch (e) { console.error("Grafik error", e); }

    // ▼▼▼ KODE PETA YANG DIPERBARUI ▼▼▼
    const mapElement = document.getElementById('map');
    try {
        const locations = @json($locations);
        
        if (typeof L !== 'undefined' && locations.length > 0) {
            var map = L.map(mapElement).setView([-7.8225, 112.0118], 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19, attribution: '&copy; OpenStreetMap'
            }).addTo(map);

            var standardLayer = L.layerGroup();
            var clusterLayer = L.markerClusterGroup();
            var heatPoints = [];

            locations.forEach(function(loc) {
                let lat = parseFloat(loc.latitude);
                let lng = parseFloat(loc.longitude);

                if (!isNaN(lat) && !isNaN(lng)) {
                    let latLng = [lat, lng];
                    
                    // --- LOGIKA POPUP BARU ---
                    let kategori = loc.kategori_umkm ? loc.kategori_umkm.toLowerCase() : '';
                    let rowHalal = '';

                    if (kategori.includes('makanan') || kategori.includes('minuman')) {
                        let status = loc.status_halal || 'Belum Info';
                        let badgeClass = 'badge-proses';
                        if (status === 'Halal') badgeClass = 'badge-halal';
                        if (status === 'Non Halal') badgeClass = 'badge-non';
                        
                        rowHalal = `
                            <tr>
                                <td class="label-col">Kehalalan</td>
                                <td>: <span class="${badgeClass}">${status}</span></td>
                            </tr>`;
                    }

                    let popupContent = `
                        <div class="custom-popup">
                            <div class="popup-header">${loc.nama_usaha || 'UMKM'}</div>
                            <table>
                                <tr><td class="label-col">Pemilik</td><td>: ${loc.nama_pemilik || '-'}</td></tr>
                                <tr><td class="label-col">Sektor</td><td>: ${loc.sektor_usaha || '-'}</td></tr>
                                <tr><td class="label-col">Alamat</td><td>: ${loc.alamat_lengkap || '-'}</td></tr>
                                ${rowHalal}
                            </table>
                        </div>
                    `;
                    // --- AKHIR LOGIKA POPUP ---

                    let marker = L.marker(latLng).bindPopup(popupContent);
                    standardLayer.addLayer(marker);
                    clusterLayer.addLayer(marker);
                    heatPoints.push(latLng);
                }
            });
            
            var heatmapLayer = L.heatLayer(heatPoints, { radius: 25, blur: 15 });
            map.addLayer(clusterLayer); // Default layer

            // Tombol Kontrol Layer
            const btnStandard = document.getElementById('view-standard');
            const btnCluster = document.getElementById('view-cluster');
            const btnHeatmap = document.getElementById('view-heatmap');
            const btnToggleGroup = document.getElementById('view-toggle');
            const btnFindMe = document.getElementById('find-me');

            function clearLayers() { map.removeLayer(standardLayer); map.removeLayer(clusterLayer); map.removeLayer(heatmapLayer); }
            function setActiveButton(activeBtn) {
                btnToggleGroup.querySelectorAll('.btn').forEach(btn => { btn.classList.remove('active', 'btn-primary'); btn.classList.add('btn-outline-primary'); });
                activeBtn.classList.add('active', 'btn-primary'); activeBtn.classList.remove('btn-outline-primary');
            }

            btnStandard.addEventListener('click', function() { clearLayers(); map.addLayer(standardLayer); setActiveButton(this); });
            btnCluster.addEventListener('click', function() { clearLayers(); map.addLayer(clusterLayer); setActiveButton(this); });
            btnHeatmap.addEventListener('click', function() { clearLayers(); map.addLayer(heatmapLayer); setActiveButton(this); });
            btnFindMe.addEventListener('click', function() { map.locate({ setView: true, maxZoom: 16 }); });
            map.on('locationfound', e => L.marker(e.latlng).addTo(map).bindPopup("Lokasi Anda").openPopup());
        } else if (!locations || locations.length === 0) {
             document.getElementById('map-error').textContent = 'Tidak ada data lokasi UMKM.';
             document.getElementById('map-error').style.display = 'block';
        }
    } catch (e) { console.error("Peta Error", e); }

    // --- Kode Filter Kelurahan (Tetap Sama) ---
    const kecamatanFilterSelect = $('#kecamatan_id_filter');
    const kelurahanFilterSelect = $('#kelurahan_id_filter');
    const selectedKelurahanId = '{{ request('kelurahan_id') }}';
    const selectedKecamatanId = '{{ $selectedKecamatanId ?? request('kecamatan_id') }}';

    function fetchKelurahanFilter(kecamatanId) {
        if (!kecamatanId) { kelurahanFilterSelect.html('<option value="">Semua Kelurahan</option>'); return; }
        $.ajax({
            url: `/api/kelurahan/${kecamatanId}`, type: 'GET',
            success: function(data) {
                let options = '<option value="">Semua Kelurahan</option>';
                data.forEach(kelurahan => {
                    const isSelected = kelurahan.id == selectedKelurahanId ? 'selected' : '';
                    options += `<option value="${kelurahan.id}" ${isSelected}>${kelurahan.nama_kelurahan}</option>`;
                });
                kelurahanFilterSelect.html(options);
            }
        });
    }
    if(selectedKecamatanId) fetchKelurahanFilter(selectedKecamatanId);
    kecamatanFilterSelect.on('change', function() { fetchKelurahanFilter(this.value); });
});
</script>
@endpush