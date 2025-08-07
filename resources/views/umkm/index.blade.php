<h1>Daftar UMKM</h1>
<a href="{{ route('umkm.create') }}">Tambah Data UMKM</a>

@if(session('success'))
    <p>{{ session('success') }}</p>
@endif

<table border="1">
    <thead>
        <tr>
            <th>Nama Usaha</th>
            <th>Pemilik</th>
            <th>Sektor</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach($umkms as $umkm)
        <tr>
            <td>{{ $umkm->nama_usaha }}</td>
            <td>{{ $umkm->nama_pemilik }}</td>
            <td>{{ $umkm->sektor_usaha }}</td>
            <td>
                <a href="{{ route('umkm.edit', $umkm->id) }}">Edit</a>
                <form action="{{ route('umkm.destroy', $umkm->id) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('Yakin ingin menghapus?')">Hapus</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
{{ $umkms->links() }}