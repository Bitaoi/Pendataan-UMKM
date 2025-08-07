<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UmkmController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $umkms = Umkm::latest()->paginate(10); //ambil 10 data terbaru
        return view('umkm.index', compact('umkms'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //ambil data kecamatan jika diprlukan untuk dropdown
        return view('umkm.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //validasi data
        $request->validate([
            'nama_usaha' => 'required|string|max:255',
            'nama_pemilik' => 'required|string|max:255',
            'alamat' => 'required|string',
            'kontak' => 'required|string|max:20',
            'sektor_usaha' => 'required|string',
            'status_legalitas' => 'required|string',
            'kecamatan' => 'required|string',
            'path_dokumen' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048', // Contoh validasi file
    ]);

        $data = $request->all();
        
        //logika upload dokumen
        if ($request->hasFile('path_dokumen')) {
        $path = $request->file('path_dokumen')->store('dokumen_umkm', 'public');
        $data['path_dokumen'] = $path;
    }

    Umkm::create($data);

    return redirect()->route('umkm.index')->with('succes', 'Data UMKM berhasil ditambahkan!');

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
