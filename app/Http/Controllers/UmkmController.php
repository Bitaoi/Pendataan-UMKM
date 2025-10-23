<?php

namespace App\Http\Controllers;

use App\Models\Umkm;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UmkmController extends Controller
{
    /**
     * Menampilkan daftar data UMKM.
     */
    public function index()
    {
        // Memuat relasi kelurahan, dan kecamatan melalui kelurahan
        $umkms = Umkm::with('kelurahan.kecamatan')->latest()->paginate(10);
        return view('umkm.index', compact('umkms'));
    }

    /**
     * Menampilkan form untuk membuat data UMKM baru.
     */
    public function create()
    {
        $kecamatans = Kecamatan::orderBy('nama_kecamatan')->get();
        return view('umkm.create', compact('kecamatans'));
    }

    /**
     * Menyimpan data UMKM baru ke database.
     */
    public function store(Request $request)
    {
        // Validasi input dengan nama kolom yang sesuai dengan database
        $validatedData = $request->validate([
            'nama_usaha' => 'required|string|max:255',
            'nama_pemilik' => 'required|string|max:255',
            'alamat_lengkap' => 'required|string',
            'nomor_telepon' => 'required|string|max:20',
            'sektor_usaha' => 'required|string',
            'status_nib' => 'required|in:Sudah Ada,Belum Ada,Sedang Proses',
            'kelurahan_id' => 'required|exists:kelurahans,id',
            'dokumen_legalitas' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'latitude' => 'nullable|numeric|between:-98,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'nomor_kbli' => 'nullable|string',
        ]);

        // Handle file upload jika ada
        if ($request->hasFile('dokumen_legalitas')) {
            $path = $request->file('dokumen_legalitas')->store('dokumen-legalitas', 'public');
            // Simpan path ke kolom yang benar di database
            $validatedData['dokumen_legalitas_path'] = $path;
            unset($validatedData['dokumen_legalitas']);
        }

        // Hanya buat data dari input yang sudah divalidasi (lebih aman)
        Umkm::create($validatedData);

        return redirect()->route('umkm.index')->with('success', 'Data UMKM berhasil ditambahkan!');
    }

    /**
     * Menampilkan form untuk mengedit data UMKM.
     */
    public function edit(Umkm $umkm)
    {
        $kecamatans = Kecamatan::orderBy('nama_kecamatan')->get();
        return view('umkm.edit', compact('umkm', 'kecamatans'));
    }

    /**
     * Memperbarui data UMKM di dalam database.
     */
    public function update(Request $request, Umkm $umkm)
    {
        // Validasi input dengan nama kolom yang sesuai dengan database
        $validatedData = $request->validate([
            'nama_usaha' => 'required|string|max:255',
            'nama_pemilik' => 'required|string|max:255',
            'alamat_lengkap' => 'required|string',
            'nomor_telepon' => 'required|string|max:20',
            'sektor_usaha' => 'required|string',
            'status_nib' => 'required|in:Sudah Ada,Belum Ada,Sedang Proses',
            'kelurahan_id' => 'required|exists:kelurahans,id',
            'dokumen_legalitas' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'nomor_kbli' => 'nullable|string',
            'kategori_umkm' => 'required|string|in:makanan_minuman,produk_kerajinan',
        ]);

        // Cek jika ada file dokumen baru yang di-upload
        if ($request->hasFile('dokumen_legalitas')) {
            // Hapus dokumen lama jika ada
            if ($umkm->dokumen_legalitas_path) {
                Storage::disk('public')->delete($umkm->dokumen_legalitas_path);
            }
            // Simpan dokumen baru dan dapatkan path-nya
            $path = $request->file('dokumen_legalitas')->store('dokumen-legalitas', 'public');
            $validatedData['dokumen_legalitas_path'] = $path;
        }

        // Update data UMKM dengan data yang sudah divalidasi
        $umkm->update($validatedData);

        return redirect()->route('umkm.index')->with('success', 'Data UMKM berhasil diperbarui!');
    }

    /**
     * Menghapus data UMKM dari database.
     */
    public function destroy(Umkm $umkm)
    {
        // Hapus dokumen terkait jika ada, menggunakan nama kolom yang benar
        if ($umkm->dokumen_legalitas_path) {
            Storage::disk('public')->delete($umkm->dokumen_legalitas_path);
        }

        $umkm->delete();

        return redirect()->route('umkm.index')->with('success', 'Data UMKM berhasil dihapus!');
    }

    /**
     * API untuk mendapatkan data kelurahan berdasarkan kecamatan.
     */
    public function getKelurahanByKecamatan($kecamatan_id)
    {
        $kelurahans = Kelurahan::where('kecamatan_id', $kecamatan_id)->orderBy('nama_kelurahan')->get();
        return response()->json($kelurahans);
    }
}
