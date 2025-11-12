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
                            <option value="{{ $kecamatan->id }}" {{ old('kecamatan_id') == $kecamatan->id ? 'selected' : '' }}>{{ $kecamatan->nama_kecamatan }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="kelurahan_id" class="form-label">Kelurahan</label>
                    <select class="form-select" id="kelurahan_id" name="kelurahan_id" required>
                        <option selected disabled value="">Pilih Kecamatan terlebih dahulu</option>
                    </select>
                </div>
                
                {{-- ▼▼▼ KODE BARU DIMULAI DI SINI ▼▼▼ --}}
                <div class="mb-3">
                    <label for="kategori_umkm" class="form-label">Kategori UMKM</label>
                    <select class="form-select" id="kategori_umkm" name="kategori_umkm" required>
                        <option selected disabled value="">Pilih Kategori...</option>
                        <option value="makanan_minuman" {{ old('kategori_umkm') == 'makanan_minuman' ? 'selected' : '' }}>Makanan / Minuman</option>
                        <option value="produk_kerajinan" {{ old('kategori_umkm') == 'produk_kerajinan' ? 'selected' : '' }}>Produk / Kerajinan</option>
                    </select>
                </div>

                <div class="mb-3" id="status-halal-wrapper" style="display: none;">
                    <label for="status_halal" class="form-label">Status Kehalalan</label>
                    <select class="form-select" id="status_halal" name="status_halal">
                        <option selected disabled value="">Pilih Status...</option>
                        <option value="Halal" {{ old('status_halal') == 'Halal' ? 'selected' : '' }}>Halal</option>
                        <option value="Non Halal" {{ old('status_halal') == 'Non Halal' ? 'selected' : '' }}>Non Halal</option>
                        <option value="Sedang Proses" {{ old('status_halal') == 'Sedang Proses' ? 'selected' : '' }}>Sedang Proses</option>
                    </select>
                </div>
                {{-- ▲▲▲ KODE BARU BERAKHIR DI SINI ▲▲▲ --}}

                <div class="mb-3">
                    <label for="sektor_usaha" class="form-label">Sektor Usaha</label>
                    <input type="text" class="form-control" id="sektor_usaha" name="sektor_usaha" value="{{ old('sektor_usaha') }}" required placeholder="Contoh: Kuliner Khas, Kerajinan Tangan, dll.">
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
                <div class="mb-3">
                    <label for="nomor_kbli" class="form-label">Nomor KBLI (Opsional)</label>
                    <input type="text" class="form-control" id="nomor_kbli" name="nomor_kbli" value="{{ old('nomor_kbli') }}">
                </div>
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
{{-- jQuery (jika belum ada di layout utama) --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ... (Kode Peta dan Geosearch tetap sama) ...
        const latInput = document.getElementById('latitude');
        const lngInput = document.getElementById('longitude');
        const alamatInput = document.getElementById('alamat_lengkap');
        var map = L.map('map').setView([-7.8225, 112.0119], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
        var marker = L.marker([-7.8225, 112.0119], { draggable: true }).addTo(map);
        const provider = new GeoSearch.OpenStreetMapProvider();
        const searchControl = new GeoSearch.GeoSearchControl({
            provider: provider, style: 'bar', showMarker: false,
            autoClose: true, keepResult: true, searchLabel: 'Cari lokasi...'
        });
        map.addControl(searchControl);
        function updateLocationData(latlng) {
            latInput.value = latlng.lat.toFixed(7);
            lngInput.value = latlng.lng.toFixed(7);
        }
        marker.on('dragend', e => updateLocationData(e.target.getLatLng()));
        map.on('click', e => {
            marker.setLatLng(e.latlng);
            updateLocationData(e.latlng);
        });
        map.on('geosearch/showlocation', function(result) {
            const latlng = { lat: result.location.y, lng: result.location.x };
            marker.setLatLng(latlng);
            updateLocationData(latlng);
        });
        
        // --- Logika Dropdown Kecamatan & Kelurahan (Menggunakan jQuery) ---
        const kecamatanSelect = $('#kecamatan_id');
        const kelurahanSelect = $('#kelurahan_id');
        const selectedKelurahanId = '{{ old('kelurahan_id') }}';

        function fetchKelurahan(kecamatanId) {
            if (!kecamatanId) {
                kelurahanSelect.html('<option value="">Pilih Kecamatan Dulu</option>');
                return;
            }
            $.ajax({
                url: `/api/kelurahan/${kecamatanId}`,
                type: 'GET',
                success: function(data) {
                    let options = '<option selected disabled value="">Pilih Kelurahan...</option>';
                    data.forEach(kelurahan => {
                        const isSelected = kelurahan.id == selectedKelurahanId ? 'selected' : '';
                        options += `<option value="${kelurahan.id}" ${isSelected}>${kelurahan.nama_kelurahan}</option>`;
                    });
                    kelurahanSelect.html(options);
                },
                error: function() {
                    kelurahanSelect.html('<option value="">Gagal memuat kelurahan</option>');
                }
            });
        }
        if (kecamatanSelect.val()) {
            fetchKelurahan(kecamatanSelect.val());
        }
        kecamatanSelect.on('change', function() {
            fetchKelurahan(this.value);
        });


        // ▼▼▼ KODE BARU UNTUK KATEGORI -> STATUS HALAL ▼▼▼
        const kategoriSelect = document.getElementById('kategori_umkm');
        const halalWrapper = document.getElementById('status-halal-wrapper');
        const halalSelect = document.getElementById('status_halal');

        function toggleHalalField() {
            if (kategoriSelect.value === 'makanan_minuman') {
                halalWrapper.style.display = 'block';
                halalSelect.setAttribute('required', 'required');
            } else {
                halalWrapper.style.display = 'none';
                halalSelect.removeAttribute('required');
                halalSelect.value = ''; // Kosongkan nilai saat disembunyikan
            }
        }
        // Panggil saat halaman dimuat (untuk menangani 'old input' jika validasi gagal)
        toggleHalalField();
        // Panggil saat dropdown kategori diubah
        kategoriSelect.addEventListener('change', toggleHalalField);
        // ▲▲▲ AKHIR KODE BARU ▲▲▲
    });
</script>
@endpush