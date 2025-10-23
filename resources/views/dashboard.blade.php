@extends('layouts.app')

{{-- ▼▼▼ SEMUA CSS KHUSUS HALAMAN INI MASUK KE SLOT 'styles' ▼▼▼ --}}
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
        background-color: #f0f0f0; /* Latar belakang jika peta gagal dimuat */
    }
    .filter-card, .map-card { margin-bottom: 1.5rem; }
    body { font-family: 'Quicksand', sans-serif; }
    /* Style untuk tombol aktif */
    .btn-group .btn.active { background-color: #0d6efd; color: white; }
    .btn-group .btn:not(.active) { background-color: #fff; color: #0d6efd; }
    /* Pesan error */
    .map-error-overlay, .chart-error-overlay {
        display: none; color: #721c24; background-color: #f8d7da;
        border: 1px solid #f5c6cb; border-radius: 8px;
        padding: 20px; text-align: center; font-weight: bold;
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
                            {{-- Opsi kelurahan akan diisi oleh JS --}}
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
            {{-- Div untuk menampilkan error jika peta gagal --}}
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
                    {{-- Div untuk menampilkan error jika grafik gagal --}}
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


{{-- ▼▼▼ SEMUA JAVASCRIPT KHUSUS HALAMAN INI MASUK KE SLOT 'scripts' ▼▼▼ --}}
@push('scripts')
{{-- JS Leaflet --}}
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>
<script src="https://unpkg.com/leaflet.heat@0.2.0/dist/leaflet-heat.js"></script>
{{-- JS Chart --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
{{-- jQuery untuk AJAX Kelurahan --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


<script>
document.addEventListener('DOMContentLoaded', function () {
    
    // =================================================================
    // KODE GRAFIK PERTUMBUHAN (Request 1)
    // =================================================================
    const chartCanvas = document.getElementById('growthChart');
    const chartErrorDiv = document.getElementById('chart-error');
    try {
        const chartLabels = @json($chartLabels);
        const chartValues = @json($chartValues);

        if (!chartLabels || !chartValues || chartLabels.length === 0) {
            throw new Error('Data grafik tidak tersedia.');
        }

        // Sembunyikan error dan tampilkan canvas jika ada data
        chartErrorDiv.style.display = 'none';
        chartCanvas.style.display = 'block';

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
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    } catch (e) {
        console.error("Gagal memuat grafik:", e);
        chartErrorDiv.textContent = 'Gagal memuat data grafik: ' + e.message;
        chartErrorDiv.style.display = 'block';
        chartCanvas.style.display = 'none';
    }

    // =================================================================
    // KODE PETA LEAFLET (Request 1)
    // =================================================================
    const mapElement = document.getElementById('map');
    const mapErrorDiv = document.getElementById('map-error');
    try {
        const locations = @json($locations);
        
        if (typeof L === 'undefined') {
            throw new Error('Library Leaflet (L) tidak ditemukan. Periksa koneksi internet atau CDN.');
        }

        var map = L.map(mapElement).setView([-7.8225, 112.0118], 13); // Center di Kediri
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        // Cek jika data lokasi kosong
        if (!locations || locations.length === 0) {
             mapErrorDiv.textContent = 'Tidak ada data lokasi UMKM untuk ditampilkan (sesuai filter).';
             mapErrorDiv.style.display = 'block';
             mapElement.style.height = '100px'; // Kecilkan peta jika kosong
             return; // Hentikan eksekusi peta
        }

        // Sembunyikan error jika data ada
        mapErrorDiv.style.display = 'none';
        
        var standardLayer = L.layerGroup();
        var clusterLayer = L.markerClusterGroup();
        var heatPoints = [];

        locations.forEach(function(location) {
            // Pastikan lat/lng valid
            let lat = parseFloat(location.latitude);
            let lng = parseFloat(location.longitude);

            if (!isNaN(lat) && !isNaN(lng)) {
                let latLng = [lat, lng];
                let popupContent = `<b>${location.nama_usaha || 'Nama Usaha'}</b>`;
                
                standardLayer.addLayer(L.marker(latLng).bindPopup(popupContent));
                clusterLayer.addLayer(L.marker(latLng).bindPopup(popupContent));
                heatPoints.push(latLng);
            }
        });
        
        var heatmapLayer = L.heatLayer(heatPoints, { radius: 25, blur: 15 });

        // Tampilkan Layer Default (Cluster)
        map.addLayer(clusterLayer);
        
        const btnStandard = document.getElementById('view-standard');
        const btnCluster = document.getElementById('view-cluster');
        const btnHeatmap = document.getElementById('view-heatmap');
        const btnToggleGroup = document.getElementById('view-toggle');
        const btnFindMe = document.getElementById('find-me');

        function clearLayers() {
            map.removeLayer(standardLayer);
            map.removeLayer(clusterLayer);
            map.removeLayer(heatmapLayer);
        }

        function setActiveButton(activeBtn) {
            btnToggleGroup.querySelectorAll('.btn').forEach(btn => {
                btn.classList.remove('active', 'btn-primary');
                btn.classList.add('btn-outline-primary');
            });
            activeBtn.classList.add('active', 'btn-primary');
            activeBtn.classList.remove('btn-outline-primary');
        }

        btnStandard.addEventListener('click', function() { clearLayers(); map.addLayer(standardLayer); setActiveButton(this); });
        btnCluster.addEventListener('click', function() { clearLayers(); map.addLayer(clusterLayer); setActiveButton(this); });
        btnHeatmap.addEventListener('click', function() { clearLayers(); map.addLayer(heatmapLayer); setActiveButton(this); });
        btnFindMe.addEventListener('click', function() { map.locate({ setView: true, maxZoom: 16 }); });
        map.on('locationfound', e => L.marker(e.latlng).addTo(map).bindPopup("Lokasi Anda").openPopup());
        map.on('locationerror', e => alert("Tidak bisa mendapatkan lokasi Anda: " + e.message));

    } catch (e) {
        console.error("Gagal memuat peta:", e);
        mapErrorDiv.textContent = 'Gagal memuat peta: ' + e.message;
        mapErrorDiv.style.display = 'block';
        mapElement.style.display = 'none'; // Sembunyikan peta jika error
    }
    
    // =================================================================
    // KODE FILTER KECAMATAN -> KELURAHAN (Request 3)
    // =================================================================
    // Menggunakan jQuery untuk AJAX
    const kecamatanFilterSelect = $('#kecamatan_id_filter');
    const kelurahanFilterSelect = $('#kelurahan_id_filter');
    // Ambil ID kelurahan yang mungkin sudah terpilih dari request sebelumnya
    const selectedKelurahanId = '{{ request('kelurahan_id') }}';
    // Ambil ID kecamatan yang mungkin sudah terpilih dari request sebelumnya
    const selectedKecamatanId = '{{ $selectedKecamatanId ?? request('kecamatan_id') }}';


    function fetchKelurahanFilter(kecamatanId) {
        if (!kecamatanId) {
            kelurahanFilterSelect.html('<option value="">Semua Kelurahan</option>');
            return;
        }
        
        // Ganti URL API jika rute Anda berbeda
        $.ajax({
            url: `/api/kelurahan/${kecamatanId}`,
            type: 'GET',
            success: function(data) {
                let options = '<option value="">Semua Kelurahan</option>';
                data.forEach(kelurahan => {
                    // Tandai sebagai 'selected' jika ID-nya cocok
                    const isSelected = kelurahan.id == selectedKelurahanId ? 'selected' : '';
                    options += `<option value="${kelurahan.id}" ${isSelected}>${kelurahan.nama_kelurahan}</option>`;
                });
                kelurahanFilterSelect.html(options);
            },
            error: function(err) {
                console.error('Gagal mengambil data kelurahan untuk filter:', err);
                kelurahanFilterSelect.html('<option value="">Gagal memuat</option>');
            }
        });
    }

    // Panggil saat halaman dimuat, jika kecamatan sudah terpilih
    if(selectedKecamatanId) {
        fetchKelurahanFilter(selectedKecamatanId);
    }

    // Panggil saat dropdown kecamatan diubah
    kecamatanFilterSelect.on('change', function() {
        fetchKelurahanFilter(this.value);
    });

});
</script>
@endpush