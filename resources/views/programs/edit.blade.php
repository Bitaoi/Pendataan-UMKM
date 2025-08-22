@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Program: {{ $program->nama_program }}</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('programs.update', $program->id) }}" method="POST">
        @csrf
        @method('PUT') {{-- Method untuk update adalah PUT --}}
        
        <div class="mb-3">
            <label for="nama_program" class="form-label">Nama Program</label>
            <input type="text" class="form-control" id="nama_program" name="nama_program" value="{{ old('nama_program', $program->nama_program) }}" required>
        </div>
        <div class="mb-3">
            <label for="deskripsi" class="form-label">Deskripsi</label>
            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" required>{{ old('deskripsi', $program->deskripsi) }}</textarea>
        </div>
        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="tanggal_mulai" class="form-label">Tanggal Mulai</label>
                <input type="date" class="form-control" id="tanggal_mulai" name="tanggal_mulai" value="{{ old('tanggal_mulai', $program->tanggal_mulai) }}" required>
            </div>
            <div class="col-md-4 mb-3">
                <label for="tanggal_selesai" class="form-label">Tanggal Selesai</label>
                <input type="date" class="form-control" id="tanggal_selesai" name="tanggal_selesai" value="{{ old('tanggal_selesai', $program->tanggal_selesai) }}" required>
            </div>
            <div class="col-md-4 mb-3">
                <label for="kuota_peserta" class="form-label">Kuota Peserta</label>
                <input type="number" class="form-control" id="kuota_peserta" name="kuota_peserta" value="{{ old('kuota_peserta', $program->kuota_peserta) }}" required>
            </div>
        </div>
        <div class="text-end">
            <a href="{{ route('programs.index') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection
