{{-- resources/views/dashboard.blade.php --}}
@extends('layouts.app')

{{-- ▼▼▼ STYLES (Sama seperti sebelumnya) ▼▼▼ --}}
@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css"/>
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css"/>
{{-- Pastikan Heatmap CSS juga disertakan jika digunakan --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet.heat@0.2.0/dist/leaflet-heat.css"/>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;700&display=swap" rel="stylesheet">
<style>
    #map {
        height: 60vh;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        background-color: #e9e9e9; /* Warna latar belakang jika peta lambat loading */
    }
    .filter-card, .map-card { margin-bottom: 1.5rem; }
    body { font-family: 'Quicksand', sans-serif; }
    /* Style untuk tombol aktif */
    .btn-group .btn.active { background-color: #0d6efd; color: white; }
    .btn-group .btn:not(.active) { background-color: #fff; color: #0d6efd; }
    /* Tambahkan style untuk pesan error di peta */
    #map-error { color: red; text-align: center; padding: 20px; }
</style>
@endsection

{{-- ▼▼▼ KONTEN UTAMA (Sama seperti sebelumnya, pastikan ID elemen benar: map, growthChart) ▼▼▼ --}}
@section('content')
<div class="container">
    {{-- FORM FILTER --}}
    <div class="card shadow-sm filter-card">
        <div class="card-body">
            <form action="{{ route('dashboard') }}" method="GET">
                <div class="row g-3 align-items-end">
                    {{-- ... (Input filter sektor, kecamatan, kelurahan, status NIB) ... --}}
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
                                {{-- Pastikan Anda melewatkan ID yang benar --}}
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
                            {{-- Pastikan value sesuai dengan yang diharapkan controller --}}
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
                 {{-- Tombol Cari Lokasi dan Toggle Tampilan Peta --}}
                <button id="find-me" class="btn btn-info btn-sm"><i class="bi bi-geo-alt-fill"></i> Lokasi Saya</button>
                <div id="view-toggle" class="btn-group btn-group-sm" role="group">
                    <button id="view-standard" type="button" class="btn btn-outline-primary">Standar</button>
                    <button id="view-cluster" type="button" class="btn btn-primary active">Cluster</button> {{-- Cluster jadi default --}}
                    <button id="view-heatmap" type="button" class="btn btn-outline-primary">Heatmap</button>
                </div>
            </div>
        </div>
        <div class="card-body">
            {{-- Tambahkan div untuk pesan error --}}
            <div id="map-error" style="display: none;"></div>
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
                    {{-- Tambahkan div untuk pesan error grafik --}}
                    <div id="chart-error" style="display: none; color: red; text-align: center; padding: 20px;"></div>
                    <canvas id="growthChart" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>
        {{-- ... (Kolom Program Pembinaan Terpopuler) ... --}}
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

{{-- ▼▼▼ SCRIPTS (Perbaikan ada di sini) ▼▼▼ --}}
@push('scripts')
{{-- Library JS --}}
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>
<script src="https://unpkg.com/leaflet.heat@0.2.0/dist/leaflet-heat.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> {{-- Tambahkan jQuery untuk AJAX Kelurahan --}}


<script>
    // Pastikan semua dijalankan setelah DOM siap
    document.addEventListener('DOMContentLoaded', function () {

        // =================================================================
        // KODE GRAFIK PERTUMBUHAN
        // =================================================================
        const chartCanvas = document.getElementById('growthChart');
        const chartErrorDiv = document.getElementById('chart-error');
        const chartLabels = @json($chartLabels);
        const chartValues = @json($chartValues);

        if (chartCanvas) {
            try {
                // Periksa apakah ada data untuk ditampilkan
                if (chartLabels && chartLabels.length > 0 && chartValues && chartValues.length > 0) {
                     chartErrorDiv.style.display = 'none'; // Sembunyikan pesan error jika ada data
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
                } else {
                    // Tampilkan pesan jika tidak ada data
                    chartErrorDiv.textContent = 'Tidak ada data pertumbuhan UMKM untuk ditampilkan.';
                    chartErrorDiv.style.display = 'block';
                    chartCanvas.style.display = 'none'; // Sembunyikan canvas
                }
            } catch (e) {
                console.error("Gagal memuat grafik:", e);
                chartErrorDiv.textContent = 'Terjadi kesalahan saat memuat grafik.';
                chartErrorDiv.style.display = 'block';
                chartCanvas.style.display = 'none'; // Sembunyikan canvas jika error
            }
        } else {
             console.error("Elemen canvas 'growthChart' tidak ditemukan.");
             if(chartErrorDiv) {
                 chartErrorDiv.textContent = 'Komponen grafik tidak dapat dimuat.';
                 chartErrorDiv.style.display = 'block';
             }
        }

        // =================================================================
        // KODE PETA LEAFLET
        // =================================================================
        const mapElement = document.getElementById('map');
        const mapErrorDiv = document.getElementById('map-error');
        const locations = @json($locations); // Ambil data lokasi dari controller

        let map = null; // Inisialisasi map di luar try-catch
        let standardLayer = L.layerGroup();
        let clusterLayer = L.markerClusterGroup();
        let heatPoints = [];
        let heatmapLayer = null; // Definisikan di sini

        if (mapElement) {
             try {
                map = L.map(mapElement).setView([-7.8225, 112.0118], 13); // Center di Kediri

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                }).addTo(map);

                // Periksa apakah ada data lokasi
                if (locations && locations.length > 0) {
                    mapErrorDiv.style.display = 'none'; // Sembunyikan error jika ada data

                    locations.forEach(function(location) {
                        if (location.latitude && location.longitude) {
                            let latLng = [parseFloat(location.latitude), parseFloat(location.longitude)];
                            // Periksa apakah lat/lng valid
                            if (!isNaN(latLng[0]) && !isNaN(latLng[1])) {
                                let popupContent = `<b>${location.nama_usaha || 'Nama Usaha Tidak Ada'}</b>`;
                                let marker = L.marker(latLng).bindPopup(popupContent);

                                // Tambahkan ke layer yang sesuai
                                standardLayer.addLayer(L.marker(latLng).bindPopup(popupContent));
                                clusterLayer.addLayer(L.marker(latLng).bindPopup(popupContent));
                                heatPoints.push(latLng);
                            } else {
                                console.warn('Data lokasi tidak valid:', location);
                            }
                        } else {
                             console.warn('Data lokasi tidak lengkap:', location);
                        }
                    });

                    // Inisialisasi Heatmap Layer hanya jika ada data
                    if (heatPoints.length > 0) {
                        heatmapLayer = L.heatLayer(heatPoints, {
                            radius: 25,
                            blur: 15,
                            maxZoom: 17
                        });
                    }

                    // Tampilkan Layer Default (Cluster) jika ada data
                    map.addLayer(clusterLayer);

                } else {
                    // Tampilkan pesan jika tidak ada data
                    mapErrorDiv.textContent = 'Tidak ada data lokasi UMKM untuk ditampilkan.';
                    mapErrorDiv.style.display = 'block';
                    mapElement.style.backgroundColor = '#f0f0f0'; // Ganti background jika kosong
                }

                // --- Referensi Tombol ---
                const btnStandard = document.getElementById('view-standard');
                const btnCluster = document.getElementById('view-cluster');
                const btnHeatmap = document.getElementById('view-heatmap');
                const btnToggleGroup = document.getElementById('view-toggle');
                const btnFindMe = document.getElementById('find-me');

                // Fungsi untuk hapus semua layer data
                function clearLayers() {
                    if (map.hasLayer(standardLayer)) map.removeLayer(standardLayer);
                    if (map.hasLayer(clusterLayer)) map.removeLayer(clusterLayer);
                    if (heatmapLayer && map.hasLayer(heatmapLayer)) map.removeLayer(heatmapLayer);
                }

                // Fungsi untuk update tombol aktif
                function setActiveButton(activeBtn) {
                    btnToggleGroup.querySelectorAll('.btn').forEach(btn => {
                        btn.classList.remove('active', 'btn-primary');
                        btn.classList.add('btn-outline-primary');
                    });
                    activeBtn.classList.add('active', 'btn-primary');
                    activeBtn.classList.remove('btn-outline-primary');
                }

                // --- Event Listeners untuk Tombol Tampilan ---
                btnStandard.addEventListener('click', function() {
                    clearLayers();
                    map.addLayer(standardLayer);
                    setActiveButton(this);
                });

                btnCluster.addEventListener('click', function() {
                    clearLayers();
                    map.addLayer(clusterLayer);
                    setActiveButton(this);
                });

                btnHeatmap.addEventListener('click', function() {
                    clearLayers();
                    // Hanya tambahkan heatmap jika layer-nya ada (ada data)
                    if (heatmapLayer) {
                        map.addLayer(heatmapLayer);
                    }
                    setActiveButton(this);
                });

                // --- Event Listener untuk "Cari Lokasi Saya" ---
                btnFindMe.addEventListener('click', function() {
                    map.locate({ setView: true, maxZoom: 16 });
                });

                map.on('locationfound', function(e) {
                     // Hapus marker lokasi lama jika ada
                     if (window.myLocationMarker) {
                         map.removeLayer(window.myLocationMarker);
                     }
                     // Tambahkan marker baru
                     window.myLocationMarker = L.marker(e.latlng).addTo(map)
                        .bindPopup("Lokasi Anda").openPopup();
                });

                map.on('locationerror', function(e) {
                    alert("Gagal mendapatkan lokasi Anda: " + e.message);
                });


            } catch (e) {
                console.error("Gagal memuat peta:", e);
                mapErrorDiv.textContent = 'Terjadi kesalahan saat memuat peta.';
                mapErrorDiv.style.display = 'block';
                mapElement.style.display = 'none'; // Sembunyikan div peta jika error
            }
        } else {
             console.error("Elemen div 'map' tidak ditemukan.");
             if(mapErrorDiv) {
                mapErrorDiv.textContent = 'Komponen peta tidak dapat dimuat.';
                mapErrorDiv.style.display = 'block';
             }
        }

        // =================================================================
        // KODE FILTER KECAMATAN -> KELURAHAN (Menggunakan jQuery)
        // =================================================================
        const kecamatanFilterSelect = $('#kecamatan_id_filter');
        const kelurahanFilterSelect = $('#kelurahan_id_filter');
        const selectedKelurahanId = '{{ request('kelurahan_id') }}'; // Ambil kelurahan terpilih dari request

        function fetchKelurahanFilter(kecamatanId) {
            if (!kecamatanId) {
                kelurahanFilterSelect.html('<option value="">Semua Kelurahan</option>');
                return;
            }
            // Ganti URL API jika berbeda
            fetch(`/api/kelurahan/${kecamatanId}`)
                .then(response => response.json())
                .then(data => {
                    let options = '<option value="">Semua Kelurahan</option>';
                    data.forEach(kelurahan => {
                        // Tandai sebagai selected jika ID sama dengan ID dari request
                        const isSelected = kelurahan.id == selectedKelurahanId ? 'selected' : '';
                        options += `<option value="${kelurahan.id}" ${isSelected}>${kelurahan.nama_kelurahan}</option>`;
                    });
                    kelurahanFilterSelect.html(options);
                });
        }

        // Panggil saat halaman dimuat jika kecamatan sudah terpilih (misal, dari filter sebelumnya)
        if(kecamatanFilterSelect.val()) {
            fetchKelurahanFilter(kecamatanFilterSelect.val());
        }

        // Panggil saat kecamatan diubah
        kecamatanFilterSelect.on('change', function() {
            fetchKelurahanFilter(this.value);
        });

    });
</script>
@endpush