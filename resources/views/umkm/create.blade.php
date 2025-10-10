@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Formulir Data UMKM Baru</h1>

    {{-- Menampilkan error validasi jika ada --}}
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
                
                {{-- PERUBAHAN DIMULAI DARI SINI --}}
                <div class="mb-3">
                    <label for="alamat_lengkap" class="form-label">Alamat Lengkap Usaha</label> 
                    <button type="button" id="cari-koordinat" class="btn btn-info btn-sm float-end">
                        <i class="bi bi-geo-alt-fill"></i> Cari di Peta
                    </button>
                    <textarea class="form-control" id="alamat_lengkap" name="alamat_lengkap" rows="3" required>{{ old('alamat_lengkap') }}</textarea>
                    <div class="form-text">Setelah mengisi alamat, klik tombol "Cari di Peta" untuk mendapatkan koordinat otomatis.</div>
                </div>
                
                <div class="mb-3">
                    <label for="sektor_usaha" class="form-label">Sektor Usaha</label>
                    <input type="text" class="form-control" id="sektor_usaha" name="sektor_usaha" value="{{ old('sektor_usaha') }}" required>
                </div>
            </div>

            {{-- Kolom Kanan --}}
            <div class="col-md-6">
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
                <div class="mb-3">
                    <label for="status_nib" class="form-label">Status NIB</label>
                    <select class="form-select" id="status_nib" name="status_nib" required>
                        <option selected disabled value="">Pilih Status...</option>
                        <option value="Sudah Ada" {{ old('status_nib') == 'Sudah Ada' ? 'selected' : '' }}>Sudah Ada</option>
                        <option value="Belum Ada" {{ old('status_nib') == 'Belum Ada' ? 'selected' : '' }}>Belum Ada</option>
                        <option value="Sedang Proses" {{ old('status_nib') == 'Sedang Proses' ? 'selected' : '' }}>Sedang Proses</option>
                    </select>
                </div>
                
                {{-- Input Latitude dan Longitude disembunyikan --}}
                <input type="hidden" class="form-control" id="latitude" name="latitude" value="{{ old('latitude') }}">
                <input type="hidden" class="form-control" id="longitude" name="longitude" value="{{ old('longitude') }}">

                <div class="mb-3">
                    <label for="dokumen_legalitas" class="form-label">Upload Dokumen (Opsional)</label>
                    <input class="form-control" type="file" id="dokumen_legalitas" name="dokumen_legalitas">
                    <div class="form-text">Format: JPG, PNG, PDF. Maks: 2MB.</div>
                </div>
            </div>
        </div>
        <div class="text-end">
            <a href="{{ route('umkm.index') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan Data</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Script untuk dropdown Kelurahan dinamis (kode lama Anda)
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

    // --- SCRIPT BARU UNTUK GEOLOKASI OTOMATIS ---
    const cariKoordinatBtn = document.getElementById('cari-koordinat');
    const alamatInput = document.getElementById('alamat_lengkap');
    const latitudeInput = document.getElementById('latitude');
    const longitudeInput = document.getElementById('longitude');

    cariKoordinatBtn.addEventListener('click', function() {
        const alamat = alamatInput.value;
        if (!alamat) {
            alert('Silakan isi alamat terlebih dahulu.');
            return;
        }

        // Tampilkan loading feedback pada tombol
        this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Mencari...';
        this.disabled = true;

        // URL API Nominatim (Layanan geocoding dari OpenStreetMap)
        const apiUrl = `https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(alamat)}&format=json&limit=1&countrycodes=id`;

        fetch(apiUrl)
            .then(response => response.json())
            .then(data => {
                if (data.length > 0) {
                    const lat = data[0].lat;
                    const lon = data[0].lon;

                    // Masukkan hasil ke input yang tersembunyi
                    latitudeInput.value = lat;
                    longitudeInput.value = lon;

                    alert(`Koordinat berhasil ditemukan!\nLatitude: ${lat}\nLongitude: ${lon}`);
                    this.innerHTML = '<i class="bi bi-check-circle-fill"></i> Ditemukan';
                } else {
                    alert('Alamat tidak ditemukan. Coba gunakan format yang lebih spesifik (contoh: nama jalan, kelurahan, kota).');
                    this.innerHTML = '<i class="bi bi-geo-alt-fill"></i> Cari di Peta';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat mencari koordinat. Periksa koneksi internet Anda.');
                this.innerHTML = '<i class="bi bi-geo-alt-fill"></i> Cari di Peta';
            })
            .finally(() => {
                this.disabled = false;
                // Kembalikan teks tombol ke semula setelah 3 detik
                setTimeout(() => {
                     this.innerHTML = '<i class="bi bi-geo-alt-fill"></i> Cari di Peta';
                }, 3000);
            });
    });
});
</script>
@endpush