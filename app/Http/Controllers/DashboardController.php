<?php

namespace App\Http\Controllers;

use App\Models\Umkm;
use App\Models\Kecamatan;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard dengan data untuk peta dan filter.
     */
    public function index(Request $request)
    {
        // Mulai query builder untuk UMKM
        $query = Umkm::query()->whereNotNull('latitude')->whereNotNull('longitude');

        // Terapkan filter jika ada input dari request
        if ($request->filled('sektor_usaha')) {
            $query->where('sektor_usaha', $request->sektor_usaha);
        }

        if ($request->filled('kecamatan_id')) {
            $query->where('kecamatan_id', $request->kecamatan_id);
        }

        if ($request->filled('kelurahan_id')) {
            $query->where('kelurahan_id', $request->kelurahan_id);
        }

        // Filter berdasarkan status legalitas (NIB)
        if ($request->filled('status_legalitas')) {
            if ($request->status_legalitas === 'legal') {
                // Asumsi 'legal' adalah semua status selain 'Illegal' atau kosong
                $query->where('status_legalitas', '!=', 'Illegal')->whereNotNull('status_legalitas');
            } elseif ($request->status_legalitas === 'illegal') {
                $query->where(function ($q) {
                    $q->where('status_legalitas', 'Illegal')->orWhereNull('status_legalitas');
                });
            }
        }

        // Ambil data UMKM yang sudah terfilter
        $locations = $query->get();
        
        // Ambil data untuk mengisi dropdown filter
        $sectors = Umkm::select('sektor_usaha')->distinct()->orderBy('sektor_usaha')->pluck('sektor_usaha');
        $kecamatans = Kecamatan::orderBy('nama_kecamatan')->get();

        // Kirim semua data yang dibutuhkan ke view
        return view('dashboard', compact('locations', 'sectors', 'kecamatans'));
    }
}
