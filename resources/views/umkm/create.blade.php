@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h4 class="mb-0">Formulir Data UMKM Baru</h4>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('umkm.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <!-- Kolom Kiri -->
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
                                    <label for="kontak" class="form-label">Kontak (No. HP/Telepon)</label>
                                    <input type="text" class="form-control" id="kontak" name="kontak" value="{{ old('kontak') }}" required>
                                </div>
                                <div class="mb-3">
                                    <label for="alamat" class="form-label">Alamat Lengkap Usaha</label>
                                    <textarea class="form-control" id="alamat" name="alamat" rows="3" required>{{ old('alamat') }}</textarea>
                                </div>
                            </div>

                            <!-- Kolom Kanan -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="kecamatan_id" class="form-label">Kecamatan</label>
                                    <select class="form-select" id="kecamatan_id" name="kecamatan_id" required>
                                        <option value="" selected disabled>Pilih Kecamatan</option>
                                        @foreach($kecamatans as $kecamatan)
                                            <option value="{{ $kecamatan->id }}" {{ old('kecamatan_id') == $kecamatan->id ? 'selected' : '' }}>{{ $kecamatan->nama_kecamatan }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="kelurahan_id" class="form-label">Kelurahan</label>
                                    <select class="form-select" id="kelurahan_id" name="kelurahan_id" required>
                                        <option value="" selected disabled>Pilih Kecamatan Terlebih Dahulu</option>
                                    </select>
                                </div>
                                 <div class="mb-3">
                                    <label for="sektor_usaha" class="form-label">Sektor Usaha</label>
                                    <input type="text" class="form-control" id="sektor_usaha" name="sektor_usaha" value="{{ old('sektor_usaha') }}" required>
                                </div>
                                <div class="mb-3">
                                    <label for="status_legalitas" class="form-label">Status Legalitas</label>
                                    <input type="text" class="form-control" id="status_legalitas" name="status_legalitas" value="{{ old('status_legalitas') }}" required>
                                </div>
                                <div class="mb-3">
                                    <label for="path_dokumen" class="form-label">Upload Dokumen (Opsional)</label>
                                    <input class="form-control" type="file" id="path_dokumen" name="path_dokumen">
                                    <small class="text-muted">Format: JPG, PNG, PDF. Maks: 2MB.</small>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <a href="{{ route('umkm.index') }}" class="btn btn-secondary me-2">Batal</a>
                            <button type="submit" class="btn btn-primary">Simpan Data</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const kecamatanSelect = document.getElementById('kecamatan_id');
        const kelurahanSelect = document.getElementById('kelurahan_id');
        
        function fetchKelurahan(kecamatanId, selectedKelurahanId = null) {
            if (!kecamatanId) {
                kelurahanSelect.innerHTML = '<option value="" selected disabled>Pilih Kecamatan Terlebih Dahulu</option>';
                return;
            }

            fetch(`/api/kelurahan/${kecamatanId}`)
                .then(response => response.json())
                .then(data => {
                    kelurahanSelect.innerHTML = '<option value="" selected disabled>Pilih Kelurahan</option>';
                    data.forEach(function(kelurahan) {
                        const option = document.createElement('option');
                        option.value = kelurahan.id;
                        option.textContent = kelurahan.nama_kelurahan;
                        if (selectedKelurahanId && kelurahan.id == selectedKelurahanId) {
                            option.selected = true;
                        }
                        kelurahanSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error fetching kelurahan:', error);
                    kelurahanSelect.innerHTML = '<option value="" selected disabled>Gagal memuat data</option>';
                });
        }

        kecamatanSelect.addEventListener('change', function() {
            fetchKelurahan(this.value);
        });

        const oldKecamatanId = "{{ old('kecamatan_id') }}";
        const oldKelurahanId = "{{ old('kelurahan_id') }}";
        if (oldKecamatanId) {
            fetchKelurahan(oldKecamatanId, oldKelurahanId);
        }
    });
</script>
@endsection
