<?php

namespace App\Http\Controllers;

use App\Models\Umkm;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UmkmController extends Controller
{
    public function index()
    {
        $umkms = Umkm::with(['kecamatan', 'kelurahan'])->latest()->paginate(10);
        return view('umkm.index', compact('umkms'));
    }

    public function create()
    {
        $kecamatans = Kecamatan::all();
        return view('umkm.create', compact('kecamatans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_usaha' => 'required|string|max:255',
            'nama_pemilik' => 'required|string|max:255',
            'alamat' => 'required|string',
            'kontak' => 'required|string|max:20',
            'sektor_usaha' => 'required|string',
            'status_legalitas' => 'required|string',
            'kecamatan_id' => 'required|exists:kecamatans,id',
            'kelurahan_id' => 'required|exists:kelurahans,id',
            'path_dokumen' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $data = $request->all();
        
        if ($request->hasFile('path_dokumen')) {
            $path = $request->file('path_dokumen')->store('dokumen_umkm', 'public');
            $data['path_dokumen'] = $path;
        }

        Umkm::create($data);

        return redirect()->route('umkm.index')->with('success', 'Data UMKM berhasil ditambahkan!');
    }

    /**
     * Menampilkan form untuk mengedit data UMKM.
     */
    public function edit(Umkm $umkm)
    {
        $kecamatans = Kecamatan::all();
        // Variabel $umkm sudah otomatis diambil oleh Laravel (Route Model Binding)
        return view('umkm.edit', compact('umkm', 'kecamatans'));
    }

    /**
     * Memperbarui data UMKM di dalam database.
     */
    public function update(Request $request, Umkm $umkm)
    {
        $request->validate([
            'nama_usaha' => 'required|string|max:255',
            'nama_pemilik' => 'required|string|max:255',
            'alamat' => 'required|string',
            'kontak' => 'required|string|max:20',
            'sektor_usaha' => 'required|string',
            'status_legalitas' => 'required|string',
            'kecamatan_id' => 'required|exists:kecamatans,id',
            'kelurahan_id' => 'required|exists:kelurahans,id',
            'path_dokumen' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $data = $request->all();

        // Cek jika ada file dokumen baru yang di-upload
        if ($request->hasFile('path_dokumen')) {
            // Hapus dokumen lama jika ada
            if ($umkm->path_dokumen) {
                Storage::disk('public')->delete($umkm->path_dokumen);
            }
            // Simpan dokumen baru
            $path = $request->file('path_dokumen')->store('dokumen_umkm', 'public');
            $data['path_dokumen'] = $path;
        }

        $umkm->update($data);

        return redirect()->route('umkm.index')->with('success', 'Data UMKM berhasil diperbarui!');
    }

    /**
     * Menghapus data UMKM dari database.
     */
    public function destroy(Umkm $umkm)
    {
        // Hapus dokumen terkait jika ada
        if ($umkm->path_dokumen) {
            Storage::disk('public')->delete($umkm->path_dokumen);
        }

        // Hapus data dari database
        $umkm->delete();

        // Arahkan kembali ke halaman index dengan pesan sukses
        return redirect()->route('umkm.index')->with('success', 'Data UMKM berhasil dihapus!');
    }

    public function getKelurahanByKecamatan($kecamatan_id)
    {
        $kelurahans = Kelurahan::where('kecamatan_id', $kecamatan_id)->get();
        return response()->json($kelurahans);
    }
}
