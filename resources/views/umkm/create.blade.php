<h1>Tambah Data UMKM Baru</h1>
<form action="{{ route('umkm.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <label>Nama Usaha:</label><br>
    <input type="text" name="nama_usaha" required><br>

    <label>Nama Pemilik:</label><br>
    <input type="text" name="nama_pemilik" required><br>

    <label>Upload Dokumen (Izin Usaha):</label><br>
    <input type="file" name="path_dokumen"><br><br>

    <button type="submit">Simpan</button>
</form>