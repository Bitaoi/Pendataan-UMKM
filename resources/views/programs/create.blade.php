@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Buat Program Baru</h1>
    <form action="{{ route('programs.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="nama_program" class="form-label">Nama Program</label>
            <input type="text" class="form-control" id="nama_program" name="nama_program" required>
        </div>
        <div class="mb-3">
            <label for="deskripsi" class="form-label">Deskripsi</label>
            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" required></textarea>
        </div>
        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="tanggal_mulai" class="form-label">Tanggal Mulai</label>
                <input type="date" class="form-control" id="tanggal_mulai" name="tanggal_mulai" required>
            </div>
            <div class="col-md-4 mb-3">
                <label for="tanggal_selesai" class="form-label">Tanggal Selesai</label>
                <input type="date" class="form-control" id="tanggal_selesai" name="tanggal_selesai" required>
            </div>
            <div class="col-md-4 mb-3">
                <label for="kuota_peserta" class="form-label">Kuota Peserta</label>
                <input type="number" class="form-control" id="kuota_peserta" name="kuota_peserta" required>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Simpan Program</button>
    </form>
</div>
@endsection