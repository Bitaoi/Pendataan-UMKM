@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Detail Program: {{ $program->nama_program }}</h2>
    <p>{{ $program->deskripsi }}</p>
    <hr>

    <div class="card mb-4">
        <div class="card-header">Manajemen Peserta ({{ $program->pesertas->count() }} / {{ $program->kuota_peserta }})</div>
        <div class="card-body">
            <form action="{{ route('programs.addPeserta', $program->id) }}" method="POST" class="mb-3">
                @csrf
                <div class="input-group">
                    <select name="umkm_id" class="form-select">
                        <option selected disabled>Pilih UMKM untuk didaftarkan...</option>
                        @foreach($umkmsNotInProgram as $umkm)
                            <option value="{{ $umkm->id }}">{{ $umkm->nama_usaha }}</option>
                        @endforeach
                    </select>
                    <button class="btn btn-success" type="submit">Tambahkan</button>
                </div>
            </form>
            <ul class="list-group">
                @forelse($program->pesertas as $peserta)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        {{ $peserta->nama_usaha }}
                        <form action="{{ route('programs.removePeserta', [$program->id, $peserta->id]) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">Keluarkan</button>
                        </form>
                    </li>
                @empty
                    <li class="list-group-item text-center">Belum ada peserta.</li>
                @endforelse
            </ul>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Pelacakan Progres</div>
        <div class="card-body">
            <form action="{{ route('programs.addLog', $program->id) }}" method="POST" class="mb-3">
                @csrf
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label>Pilih Peserta</label>
                        <select name="umkm_id" class="form-select" required>
                            @foreach($program->pesertas as $peserta)
                                <option value="{{ $peserta->id }}">{{ $peserta->nama_usaha }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Tanggal</label>
                        <input type="date" name="tanggal_log" class="form-control" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label>Deskripsi Progres/Aktivitas</label>
                    <textarea name="deskripsi_progres" class="form-control" rows="2" required></textarea>
                </div>
                <button type="submit" class="btn btn-info">Catat Progres</button>
            </form>

            <h5>Riwayat Progres:</h5>
            @forelse($program->logs->sortByDesc('tanggal_log') as $log)
            <div class="alert alert-light">
                <strong>{{ $log->umkm->nama_usaha }}</strong> - [{{ $log->tanggal_log }}] <br>
                {{ $log->deskripsi_progres }}
                <small class="d-block text-muted">Dicatat oleh: {{ $log->user->name }}</small>
            </div>
            @empty
            <p class="text-center">Belum ada catatan progres.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection