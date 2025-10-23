@extends('layouts.app')

@section('styles')
{{-- CSS untuk Leaflet --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
{{-- CSS untuk Plugin Pencarian (Leaflet GeoSearch) --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet-geosearch@3.11.0/dist/geosearch.css"/>

<style>
    #map { height: 450px; z-index: 1; border-radius: 8px; }
    .leaflet-geosearch-bar {
        z-index: 1000;
        border-radius: 4px;
        border: 2px solid rgba(0,0,0,0.2);
    }
</style>
@endsection


@section('content')
<div class="container">
    <h1>Formulir Data UMKM Baru</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('umkm.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            {{-- Kolom Kiri --}}
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="nama_usaha" class="form-label">Nama Usaha</label>
                    <input type="text" class="form-control" id="nama_usaha" name="nama_usaha" value="{{ old('nama_usaha') }}" required>
                </div>
                <div class="mb-3">
                    <label for="nama_pemilik" class="form-label">Nama Pemilik</label>
                    <input type="text" class="form-control" id="nama_pemilik" name="nama_pemilik" value="{{ old('nama_pemilik') }}" required>
                </div>
                <div class="mb-3">
                    <label for="nomor_telepon" class="form-label">Kontak (No. HP/Telepon)</label>
                    <input type="text" class="form-control" id="nomor_telepon" name="nomor_telepon" value="{{ old('nomor_telepon') }}" required>
                </div>
                <div class="mb-3">
                    <label for="alamat_lengkap" class="form-label">Alamat Lengkap Usaha</label>
                    <textarea class="form-control" id="alamat_lengkap" name="alamat_lengkap" rows="3" required>{{ old('alamat_lengkap') }}</textarea>
                </div>
                 <div class="mb-3">
                    <label for="kecamatan_id" class="form-label">Kecamatan</label>
                    <select class="form-select" id="kecamatan_id" name="kecamatan_id" required>
                        <option selected disabled value="">Pilih Kecamatan...</option>
                        @foreach($kecamatans as $kecamatan)
                            <option value="{{ $kecamatan->id }}">{{ $kecamatan->nama_kecamatan }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="kelurahan_id" class="form-label">Kelurahan</label>
                    <select class="form-select" id="kelurahan_id" name="kelurahan_id" required>
                        <option selected disabled value="">Pilih Kecamatan terlebih dahulu</option>
                    </select>
                </div>

                {{-- ▼▼▼ TAMBAHKAN DROPDOWN KATEGORI DI SINI ▼▼▼ --}}
                <div class="mb-3">
                    <label for="kategori_umkm" class="form-label">Kategori UMKM</label>
                    <select class="form-select" id="kategori_umkm" name="kategori_umkm" required>
                        <option selected disabled value="">Pilih Kategori...</option>
                        <option value="makanan_minuman" {{ old('kategori_umkm') == 'makanan_minuman' ? 'selected' : '' }}>Makanan / Minuman</option>
                        <option value="produk_kerajinan" {{ old('kategori_umkm') == 'produk_kerajinan' ? 'selected' : '' }}>Produk / Kerajinan</option>
                        {{-- Tambahkan kategori lain jika perlu --}}
                    </select>
                </div>
                {{-- ▲▲▲ AKHIR TAMBAHAN DROPDOWN KATEGORI ▲▲▲ --}}


                <div class="mb-3">
                    <label for="sektor_usaha" class="form-label">Sektor Usaha</label>
                    <input type="text" class="form-control" id="sektor_usaha" name="sektor_usaha" value="{{ old('sektor_usaha') }}" required placeholder="Contoh: Kuliner Khas, Kerajinan Tangan, Fashion, Jasa, dll.">
                    <div class="form-text">Jelaskan lebih spesifik jenis usaha Anda.</div>
                </div>
                
                {{-- ▼▼▼ FIELD YANG HILANG DITAMBAHKAN KEMBALI DI SINI ▼▼▼ --}}
                <div class="mb-3">
                    <label for="sektor_usaha" class="form-label">Sektor Usaha</label>
                    <input type="text" class="form-control" id="sektor_usaha" name="sektor_usaha" value="{{ old('sektor_usaha') }}" required>
                </div>
                <div class="mb-3">
                    <label for="status_nib" class="form-label">Status NIB</label>
                    <select class="form-select" id="status_nib" name="status_nib" required>
                        <option selected disabled value="">Pilih Status...</option>
                        <option value="Sudah Ada" {{ old('status_nib') == 'Sudah Ada' ? 'selected' : '' }}>Sudah Ada</option>
                        <option value="Belum Ada" {{ old('status_nib') == 'Belum Ada' ? 'selected' : '' }}>Belum Ada</option>
                        <option value="Sedang Proses" {{ old('status_nib') == 'Sedang Proses' ? 'selected' : '' }}>Sedang Proses</option>
                    </select>
                </div>
                {{-- ▲▲▲ AKHIR DARI FIELD YANG DITAMBAHKAN KEMBALI ▲▲▲ --}}

            </div>

            {{-- Kolom Kanan --}}
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Cari & Pilih Lokasi di Peta</label>
                    <div id="map"></div>
                </div>

                <div class="row">
                    <div class="col-6">
                        <div class="mb-3">
                            <label for="latitude" class="form-label">Latitude</label>
                            <input type="text" class="form-control" id="latitude" name="latitude" value="{{ old('latitude') }}" readonly>
                        </div>
                    </div>
                    <div class="col-6">
                         <div class="mb-3">
                            <label for="longitude" class="form-label">Longitude</label>
                            <input type="text" class="form-control" id="longitude" name="longitude" value="{{ old('longitude') }}" readonly>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="dokumen_legalitas" class="form-label">Upload Dokumen (Opsional)</label>
                    <input class="form-control" type="file" id="dokumen_legalitas" name="dokumen_legalitas">
                    <div class="form-text">Format: JPG, PNG, PDF. Maks: 2MB.</div>
                </div>
            </div>
        </div>
        <div class="text-end mt-4">
            <a href="{{ route('umkm.index') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan Data</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
{{-- JS untuk Leaflet --}}
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
{{-- JS untuk Plugin Pencarian (Leaflet GeoSearch) --}}
<script src="https://unpkg.com/leaflet-geosearch@3.11.0/dist/geosearch.umd.js"></script>

<script>
    // ... (Semua kode JavaScript untuk peta, reverse geocoding, dan dropdown dinamis tetap sama, tidak perlu diubah)
    document.addEventListener('DOMContentLoaded', function() {
        // Inisialisasi Elemen Form
        const latInput = document.getElementById('latitude');
        const lngInput = document.getElementById('longitude');
        const alamatInput = document.getElementById('alamat_lengkap');

        // Inisialisasi Peta
        var map = L.map('map').setView([-7.8225, 112.0119], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
        var marker = L.marker([-7.8225, 112.0119], { draggable: true }).addTo(map);

        // Menambahkan Kotak Pencarian
        const provider = new GeoSearch.OpenStreetMapProvider();
        const searchControl = new GeoSearch.GeoSearchControl({
            provider: provider,
            style: 'bar',
            showMarker: false,
            autoClose: true,
            keepResult: true,
            searchLabel: 'Cari kecamatan/kelurahan...'
        });
        map.addControl(searchControl);

        // Fungsi Reverse Geocoding
        function reverseGeocode(latlng) {
            alamatInput.value = 'Mencari alamat...';
            const apiUrl = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${latlng.lat}&lon=${latlng.lng}`;
            fetch(apiUrl)
                .then(response => response.json())
                .then(data => {
                    alamatInput.value = (data && data.display_name) ? data.display_name : 'Alamat tidak ditemukan.';
                });
        }

        // Fungsi update data lokasi
        function updateLocationData(latlng) {
            latInput.value = latlng.lat.toFixed(7);
            lngInput.value = latlng.lng.toFixed(7);
            reverseGeocode(latlng);
        }

        // Event Listener untuk interaksi peta manual
        marker.on('dragend', e => updateLocationData(e.target.getLatLng()));
        map.on('click', e => {
            marker.setLatLng(e.latlng);
            updateLocationData(e.latlng);
        });

        // Menghubungkan Hasil Pencarian dengan Marker
        map.on('geosearch/showlocation', function(result) {
            const latlng = { lat: result.location.y, lng: result.location.x };
            marker.setLatLng(latlng);
            updateLocationData(latlng);
        });

        // Set nilai awal
        updateLocationData(marker.getLatLng());

        // Dropdown dinamis
        document.getElementById('kecamatan_id').addEventListener('change', function() {
            var kecamatanId = this.value;
            var kelurahanSelect = document.getElementById('kelurahan_id');
            kelurahanSelect.innerHTML = '<option value="">Memuat...</option>';

            if (kecamatanId) {
                fetch(`/api/kelurahan/${kecamatanId}`)
                    .then(response => response.json())
                    .then(data => {
                        kelurahanSelect.innerHTML = '<option selected disabled value="">Pilih Kelurahan...</option>';
                        data.forEach(function(kelurahan) {
                            var option = document.createElement('option');
                            option.value = kelurahan.id;
                            option.textContent = kelurahan.nama_kelurahan;
                            kelurahanSelect.appendChild(option);
                        });
                    });
            } else {
                kelurahanSelect.innerHTML = '<option selected disabled value="">Pilih Kecamatan terlebih dahulu</option>';
            }
        });
    });
</script>
@endpush