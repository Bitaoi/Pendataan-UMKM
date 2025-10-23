@extends('layouts.app')

{{-- Menambahkan CSS untuk Peta dan Font --}}
@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<link rel="stylesheet" href="https://unpkg.com/leaflet-geosearch@3.11.0/dist/geosearch.css"/>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;700&display=swap" rel="stylesheet">
<style>
    body { font-family: 'Quicksand', sans-serif; }
    #map { height: 450px; z-index: 1; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    .leaflet-geosearch-bar { z-index: 1000; }
</style>
@endsection


@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-12"> {{-- Dibuat lebih lebar untuk mengakomodasi 2 kolom --}}
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h4 class="mb-0 fw-bold">Edit Data UMKM: {{ $umkm->nama_usaha }}</h4>
                </div>
                <div class="card-body p-4">
                    {{-- Menampilkan error validasi jika ada --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('umkm.update', $umkm->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nama_usaha" class="form-label">Nama Usaha</label>
                                    <input type="text" class="form-control" id="nama_usaha" name="nama_usaha" value="{{ old('nama_usaha', $umkm->nama_usaha) }}" required>
                                </div>
                                <div class="mb-3">
                                    <label for="nama_pemilik" class="form-label">Nama Pemilik</label>
                                    <input type="text" class="form-control" id="nama_pemilik" name="nama_pemilik" value="{{ old('nama_pemilik', $umkm->nama_pemilik) }}" required>
                                </div>
                                <div class="mb-3">
                                    <label for="nomor_telepon" class="form-label">Kontak (No. HP/Telepon)</label>
                                    <input type="text" class="form-control" id="nomor_telepon" name="nomor_telepon" value="{{ old('nomor_telepon', $umkm->nomor_telepon) }}" required>
                                </div>
                                <div class="mb-3">
                                    <label for="alamat_lengkap" class="form-label">Alamat Lengkap Usaha</label>
                                    <textarea class="form-control" id="alamat_lengkap" name="alamat_lengkap" rows="3" required>{{ old('alamat_lengkap', $umkm->alamat_lengkap) }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="kecamatan_id" class="form-label">Kecamatan</label>
                                    <select class="form-select" id="kecamatan_id" name="kecamatan_id" required>
                                        <option disabled value="">Pilih Kecamatan</option>
                                        @foreach($kecamatans as $kecamatan)
                                            {{-- Gunakan data dari relasi untuk 'selected' --}}
                                            <option value="{{ $kecamatan->id }}" {{ old('kecamatan_id', $umkm->kelurahan->kecamatan_id ?? '') == $kecamatan->id ? 'selected' : '' }}>
                                                {{ $kecamatan->nama_kecamatan }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="kelurahan_id" class="form-label">Kelurahan</label>
                                    <select class="form-select" id="kelurahan_id" name="kelurahan_id" required>
                                        {{-- Akan diisi oleh JavaScript --}}
                                        <option value="" selected disabled>Pilih Kecamatan Terlebih Dahulu</option>
                                    </select>
                                </div>

                                {{-- ▼▼▼ INI ADALAH TAMBAHAN BARU (SESUAI PERMINTAAN) ▼▼▼ --}}
                                <div class="mb-3">
                                    <label for="kategori_umkm" class="form-label">Kategori UMKM</label>
                                    <select class="form-select" id="kategori_umkm" name="kategori_umkm" required>
                                        <option selected disabled value="">Pilih Kategori...</option>
                                        {{-- Periksa nilai 'old' atau nilai dari database $umkm --}}
                                        <option value="makanan_minuman" {{ old('kategori_umkm', $umkm->kategori_umkm) == 'makanan_minuman' ? 'selected' : '' }}>Makanan / Minuman</option>
                                        <option value="produk_kerajinan" {{ old('kategori_umkm', $umkm->kategori_umkm) == 'produk_kerajinan' ? 'selected' : '' }}>Produk / Kerajinan</option>
                                    </select>
                                </div>
                                {{-- ▲▲▲ AKHIR TAMBAHAN BARU ▲▲▲ --}}

                                <div class="mb-3">
                                    <label for="sektor_usaha" class="form-label">Sektor Usaha</label>
                                    <input type="text" class="form-control" id="sektor_usaha" name="sektor_usaha" value="{{ old('sektor_usaha', $umkm->sektor_usaha) }}" required placeholder="Contoh: Kuliner Khas, Kerajinan Tangan, dll.">
                                </div>
                                <div class="mb-3">
                                    <label for="status_nib" class="form-label">Status NIB</label>
                                    <select class="form-select" id="status_nib" name="status_nib" required>
                                        <option disabled value="">Pilih Status...</option>
                                        <option value="Sudah Ada" {{ old('status_nib', $umkm->status_nib) == 'Sudah Ada' ? 'selected' : '' }}>Sudah Ada</option>
                                        <option value="Belum Ada" {{ old('status_nib', $umkm->status_nib) == 'Belum Ada' ? 'selected' : '' }}>Belum Ada</option>
                                        <option value="Sedang Proses" {{ old('status_nib', $umkm->status_nib) == 'Sedang Proses' ? 'selected' : '' }}>Sedang Proses</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="nomor_kbli" class="form-label">Nomor KBLI (Opsional)</label>
                                    <input type="text" class="form-control" id="nomor_kbli" name="nomor_kbli" value="{{ old('nomor_kbli', $umkm->nomor_kbli) }}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Cari & Pilih Lokasi di Peta</label>
                                    <div id="map"></div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="mb-3">
                                            <label for="latitude" class="form-label">Latitude</label>
                                            <input type="text" class="form-control" id="latitude" name="latitude" value="{{ old('latitude', $umkm->latitude) }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="mb-3">
                                            <label for="longitude" class="form-label">Longitude</label>
                                            <input type="text" class="form-control" id="longitude" name="longitude" value="{{ old('longitude', $umkm->longitude) }}" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="dokumen_legalitas" class="form-label">Upload Dokumen Baru (Opsional)</label>
                                    <input class="form-control" type="file" id="dokumen_legalitas" name="dokumen_legalitas">
                                    <small class="text-muted">Kosongkan jika tidak ingin mengubah dokumen.</small>
                                    
                                    {{-- PERBAIKAN: Gunakan nama kolom 'dokumen_legalitas_path' dari database --}}
                                    @if($umkm->dokumen_legalitas_path)
                                        <small class="text-muted mt-1 d-block">Dokumen saat ini: <a href="{{ asset('storage/' . $umkm->dokumen_legalitas_path) }}" target="_blank">Lihat Dokumen</a></small>
                                    @else
                                         <small class="text-muted mt-1 d-block">Dokumen saat ini: Tidak ada.</small>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <a href="{{ route('umkm.index') }}" class="btn btn-secondary me-2">Batal</a>
                            <button type="submit" class="btn btn-primary">Update Data</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Library JavaScript untuk Peta --}}
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-geosearch@3.11.0/dist/geosearch.umd.js"></script>
{{-- Tambahkan jQuery jika belum ada di layout utama, untuk AJAX Kelurahan --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inisialisasi Elemen Form
    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');
    const alamatInput = document.getElementById('alamat_lengkap');

    // Ambil koordinat awal dari data UMKM yang diedit.
    // Jika tidak ada, gunakan lokasi default (misal: Kediri).
    // Pastikan nilai default adalah angka
    const initialLat = parseFloat("{{ old('latitude', $umkm->latitude ?? -7.8225) }}") || -7.8225;
    const initialLng = parseFloat("{{ old('longitude', $umkm->longitude ?? 112.0119) }}") || 112.0119;
    const initialZoom = {{ $umkm->latitude ? 16 : 13 }}; // Zoom lebih dekat jika lokasi sudah ada

    // Inisialisasi Peta
    var map = L.map('map').setView([initialLat, initialLng], initialZoom);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
    var marker = L.marker([initialLat, initialLng], { draggable: true }).addTo(map);

    // Menambahkan Kotak Pencarian
    const provider = new GeoSearch.OpenStreetMapProvider();
    const searchControl = new GeoSearch.GeoSearchControl({
        provider: provider, style: 'bar', showMarker: false,
        autoClose: true, keepResult: true, searchLabel: 'Cari lokasi...'
    });
    map.addControl(searchControl);

    // Fungsi Reverse Geocoding (Opsional, tapi membantu)
    function reverseGeocode(latlng) {
        // Hanya update alamat jika user tidak sedang mengetik manual
        if (document.activeElement !== alamatInput) {
            alamatInput.value = 'Mencari alamat...';
            const apiUrl = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${latlng.lat}&lon=${latlng.lng}`;
            fetch(apiUrl)
                .then(response => response.json())
                .then(data => {
                    alamatInput.value = (data && data.display_name) ? data.display_name : 'Alamat tidak ditemukan.';
                });
        }
    }

    // Fungsi update data lokasi
    function updateLocationData(latlng) {
        latInput.value = latlng.lat.toFixed(7);
        lngInput.value = latlng.lng.toFixed(7);
        // reverseGeocode(latlng); // Aktifkan jika ingin alamat terisi otomatis
    }

    // Event Listener untuk interaksi peta
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

    // --- Logika Dropdown Kecamatan & Kelurahan ---
    const kecamatanSelect = document.getElementById('kecamatan_id');
    const kelurahanSelect = document.getElementById('kelurahan_id');
    // Ambil ID kelurahan yang sedang diedit
    const selectedKelurahanId = '{{ old('kelurahan_id', $umkm->kelurahan_id) }}';

    function fetchKelurahan(kecamatanId) {
        if (!kecamatanId) {
            kelurahanSelect.innerHTML = '<option value="">Pilih Kecamatan Dulu</option>';
            return;
        }
        // Pastikan URL API Anda benar
        fetch(`/api/kelurahan/${kecamatanId}`)
            .then(response => response.json())
            .then(data => {
                let options = '<option selected disabled value="">Pilih Kelurahan...</option>';
                data.forEach(kelurahan => {
                    // Cek apakah kelurahan ini adalah kelurahan yang sedang dipilih
                    const isSelected = kelurahan.id == selectedKelurahanId ? 'selected' : '';
                    options += `<option value="${kelurahan.id}" ${isSelected}>${kelurahan.nama_kelurahan}</option>`;
                });
                kelurahanSelect.innerHTML = options;
            })
            .catch(error => {
                console.error('Error fetching kelurahan:', error);
                kelurahanSelect.innerHTML = '<option value="">Gagal memuat kelurahan</option>';
            });
    }
    
    // Panggil saat halaman dimuat untuk mengisi daftar kelurahan awal
    if(kecamatanSelect.value) {
        fetchKelurahan(kecamatanSelect.value);
    }

    kecamatanSelect.addEventListener('change', function() {
        fetchKelurahan(this.value);
    });
});
</script>
@endpush