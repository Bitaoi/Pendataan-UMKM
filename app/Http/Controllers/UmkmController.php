<?php

namespace App\Http\Controllers;

use App\Models\Umkm;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // Penting untuk mengelola file

class UmkmController extends Controller
{
    /**
     * Menampilkan daftar data UMKM.
     */
    public function index()
    {
        // Mengambil 10 data UMKM terbaru dengan relasi kecamatan dan kelurahan
        $umkms = Umkm::with(['kecamatan', 'kelurahan'])->latest()->paginate(10);
        return view('umkm.index', compact('umkms'));
    }

    /**
     * Menampilkan form untuk membuat data UMKM baru.
     */
    public function create()
    {
        // Mengambil semua data kecamatan untuk dropdown
        $kecamatans = Kecamatan::all();
        return view('umkm.create', compact('kecamatans'));
    }
    

    /**
     * Menyimpan data UMKM baru ke dalam database.
     */
    public function store(Request $request)
    {
        // Validasi data yang masuk dari form
        $request->validate([
            'nama_usaha' => 'required|string|max:255',
            'nama_pemilik' => 'required|string|max:255',
            'alamat' => 'required|string',
            'kontak' => 'required|string|max:20',
            'sektor_usaha' => 'required|string',
            'status_legalitas' => 'required|string',
            'kecamatan_id' => 'required|exists:kecamatans,id',
            'kelurahan_id' => 'required|exists:kelurahans,id',
            'path_dokumen' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048', // Dokumen opsional
        ]);

        $data = $request->all();
        
        // Logika untuk upload dokumen jika ada
        if ($request->hasFile('path_dokumen')) {
            $path = $request->file('path_dokumen')->store('dokumen_umkm', 'public');
            $data['path_dokumen'] = $path;
        }

        Umkm::create($data);

        // Redirect kembali ke halaman index dengan pesan sukses
        return redirect()->route('umkm.index')->with('success', 'Data UMKM berhasil ditambahkan!');
    }

    /**
     * Menampilkan form untuk mengedit data.
     * (Akan kita implementasikan nanti)
     */
    public function edit(Umkm $umkm)
    {
        // Logika untuk halaman edit akan ditambahkan di sini
    }

    /**
     * Memperbarui data di database.
     * (Akan kita implementasikan nanti)
     */
    public function update(Request $request, Umkm $umkm)
    {
        // Logika untuk update data akan ditambahkan di sini
    }

    /**
     * Menghapus data dari database.
     * (Akan kita implementasikan nanti)
     */
    public function destroy(Umkm $umkm)
    {
        // Logika untuk hapus data akan ditambahkan di sini
    }

    /**
     * API endpoint untuk mendapatkan daftar kelurahan berdasarkan kecamatan.
     * Ini digunakan oleh JavaScript di form create.
     */
    public function getKelurahanByKecamatan($kecamatan_id)
    {
        $kelurahans = \App\Models\Kelurahan::where('kecamatan_id', $kecamatan_id)->get();
        return response()->json($kelurahans);
    }
}
