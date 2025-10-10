@extends('layouts.app')

@section('content')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">

<style>
    body {
        font-family: 'Poppins', sans-serif;
    }
</style>

<div class="container">
    <h1>Manajemen Program Pembinaan</h1>

    {{-- Tombol "Buat Program Baru" dengan sintaks yang sudah diperbaiki --}}
    <a href="{{ route('programs.create') }}" class="btn btn-lemon mb-3">Buat Program Baru</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nama Program</th>
                <th>Periode</th>
                <th>Peserta / Kuota</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($programs as $program)
            <tr>
                <td>{{ $program->nama_program }}</td>
                <td>{{ $program->tanggal_mulai }} s/d {{ $program->tanggal_selesai }}</td>
                <td>{{ $program->pesertas_count }} / {{ $program->kuota_peserta }}</td>
                <td>
                    <a href="{{ route('programs.show', $program->id) }}" class="btn btn-sm btn-info">Detail</a>
                    <a href="{{ route('programs.edit', $program->id) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('programs.destroy', $program->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus program ini?')">Hapus</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="4" class="text-center">Belum ada program.</td></tr>
            @endforelse
        </tbody>
    </table>
    {{ $programs->links() }}
</div>
@endsection