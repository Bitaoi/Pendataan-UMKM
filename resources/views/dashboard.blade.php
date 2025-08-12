@extends('layouts.app')

@section('content')
{{-- Memuat CSS untuk Leaflet & Plugin --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css"/>
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css"/>

<style>
    #map { 
        height: 60vh; /* Tinggi peta 60% dari tinggi viewport */
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .filter-card {
        margin-bottom: 1.5rem;
    }
    .legend {
        padding: 6px 8px; font: 14px/16px Arial, Helvetica, sans-serif;
        background: white; background: rgba(255,255,255,0.8);
        box-shadow: 0 0 15px rgba(0,0,0,0.2); border-radius: 5px;
    }
    .legend i {
        width: 18px; height: 18px; float: left;
        margin-right: 8px; opacity: 0.9; border-radius: 50%;
    }
</style>

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
    <div class="card shadow-sm">
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
</div>

{{-- Memuat JS untuk Leaflet & Plugin --}}
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>
<script src="https://unpkg.com/leaflet.heat@0.2.0/dist/leaflet-heat.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. INISIALISASI PETA
    var map = L.map('map').setView([-7.8216, 112.0150], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19, attribution: '© OpenStreetMap'
    }).addTo(map);

    // 2. AMBIL DATA DARI CONTROLLER
    var locations = @json($locations);
    var allSectors = @json($sectors);

    // 3. PENGATURAN WARNA & IKON
    var colorPalette = ['#e6194B', '#3cb44b', '#ffe119', '#4363d8', '#f58231', '#911eb4', '#42d4f4', '#f032e6'];
    var sectorColorMap = {};
    allSectors.forEach((sector, index) => {
        sectorColorMap[sector] = colorPalette[index % colorPalette.length] || '#7f8c8d';
    });

    function createCustomIcon(color) {
        var markerHtmlStyles = `background-color: ${color}; width: 2rem; height: 2rem; display: block; left: -1rem; top: -1.5rem; position: relative; border-radius: 2rem 2rem 0; transform: rotate(45deg); border: 1px solid #FFFFFF`;
        return L.divIcon({ className: "my-custom-pin", iconAnchor: [0, 24], popupAnchor: [0, -36], html: `<span style="${markerHtmlStyles}" />` });
    }

    // 4. BUAT LAYER UNTUK SETIAP TAMPILAN
    var standardLayer = L.layerGroup();
    var clusterLayer = L.markerClusterGroup();
    var heatLayer = L.heatLayer([], { radius: 25 });
    var heatPoints = [];

    locations.forEach(function(loc) {
        if (loc.latitude && loc.longitude) {
            var color = sectorColorMap[loc.sektor_usaha] || '#7f8c8d';
            var icon = createCustomIcon(color);
            var popupContent = `<b>${loc.nama_usaha}</b><br><b>Sektor:</b> ${loc.sektor_usaha}<br><b>Pemilik:</b> ${loc.nama_pemilik}`;
            
            // Tambahkan ke setiap layer
            standardLayer.addLayer(L.marker([loc.latitude, loc.longitude], {icon: icon}).bindPopup(popupContent));
            clusterLayer.addLayer(L.marker([loc.latitude, loc.longitude], {icon: icon}).bindPopup(popupContent));
            heatPoints.push([loc.latitude, loc.longitude, 0.5]); // 0.5 adalah intensitas
        }
    });
    heatLayer.setLatLngs(heatPoints);

    // 5. LOGIKA PERGANTIAN TAMPILAN
    var currentViewLayer = clusterLayer; // Tampilan default adalah cluster
    map.addLayer(currentViewLayer);

    function switchView(viewType) {
        map.removeLayer(currentViewLayer);
        document.querySelectorAll('.btn-group .btn').forEach(b => b.classList.remove('active'));

        if (viewType === 'standard') {
            currentViewLayer = standardLayer;
            document.getElementById('view-standard').classList.add('active');
        } else if (viewType === 'cluster') {
            currentViewLayer = clusterLayer;
            document.getElementById('view-cluster').classList.add('active');
        } else if (viewType === 'heatmap') {
            currentViewLayer = heatLayer;
            document.getElementById('view-heatmap').classList.add('active');
        }
        map.addLayer(currentViewLayer);
    }

    document.getElementById('view-standard').addEventListener('click', () => switchView('standard'));
    document.getElementById('view-cluster').addEventListener('click', () => switchView('cluster'));
    document.getElementById('view-heatmap').addEventListener('click', () => switchView('heatmap'));

    // 6. BUAT LEGEDA
    var legend = L.control({position: 'bottomright'});
    legend.onAdd = function (map) {
        var div = L.DomUtil.create('div', 'info legend');
        var labels = ['<strong>Legenda Sektor</strong>'];
        for (var sector in sectorColorMap) {
            labels.push('<i style="background:' + sectorColorMap[sector] + '"></i> ' + (sector || 'Lainnya'));
        }
        div.innerHTML = labels.join('<br>');
        return div;
    };
    legend.addTo(map);

    // 7. FITUR "CARI LOKASI SAYA"
    document.getElementById('find-me').addEventListener('click', function() {
        navigator.geolocation.getCurrentPosition(function(position) {
            var lat = position.coords.latitude;
            var lon = position.coords.longitude;
            map.setView([lat, lon], 15);
            L.marker([lat, lon], {
                icon: L.divIcon({
                    className: 'my-location-icon',
                    html: '<div style="background-color: #2980b9; width:20px; height:20px; border-radius:50%; border: 3px solid white; box-shadow: 0 0 5px #000;"></div>',
                    iconSize: [20, 20]
                })
            }).addTo(map).bindPopup('<b>Lokasi Anda</b>').openPopup();
        }, function() {
            alert('Tidak dapat mengakses lokasi Anda. Pastikan Anda memberikan izin.');
        });
    });

    // 8. LOGIKA FILTER KELURAHAN DINAMIS
    const kecamatanFilter = document.getElementById('kecamatan_id_filter');
    const kelurahanFilter = document.getElementById('kelurahan_id_filter');
    
    function fetchKelurahanForFilter(kecamatanId, selectedKelurahanId = null) {
        if (!kecamatanId) {
            kelurahanFilter.innerHTML = '<option value="">Semua Kelurahan</option>';
            return;
        }
        fetch(`/api/kelurahan/${kecamatanId}`)
            .then(response => response.json())
            .then(data => {
                let options = '<option value="">Semua Kelurahan</option>';
                data.forEach(function(kelurahan) {
                    const isSelected = selectedKelurahanId && kelurahan.id == selectedKelurahanId ? 'selected' : '';
                    options += `<option value="${kelurahan.id}" ${isSelected}>${kelurahan.nama_kelurahan}</option>`;
                });
                kelurahanFilter.innerHTML = options;
            });
    }

    kecamatanFilter.addEventListener('change', function() {
        fetchKelurahanForFilter(this.value);
    });

    // Jika halaman dimuat dengan filter kecamatan, panggil fungsi untuk mengisi kelurahan
    const initialKecamatanFilterId = "{{ request('kecamatan_id') }}";
    const initialKelurahanFilterId = "{{ request('kelurahan_id') }}";
    if (initialKecamatanFilterId) {
        fetchKelurahanForFilter(initialKecamatanFilterId, initialKelurahanFilterId);
    }
});
</script>
@endsection
