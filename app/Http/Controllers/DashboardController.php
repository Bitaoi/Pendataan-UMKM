<?php

namespace App\Http\Controllers;

use App\Models\Umkm;
use App\Models\Kecamatan;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard dengan semua data yang dibutuhkan.
     */
    public function index(Request $request)
    {
        // =====================================================================
        // BAGIAN 1: DATA UNTUK PETA INTERAKTIF DAN FILTERNYA
        // =====================================================================
        $queryPeta = Umkm::query()->whereNotNull('latitude')->whereNotNull('longitude');

        if ($request->filled('sektor_usaha')) {
            $queryPeta->where('sektor_usaha', $request->sektor_usaha);
        }
        // Catatan: Pastikan kolom kecamatan_id dan kelurahan_id ada di tabel umkms
        if ($request->filled('kecamatan_id')) {
            $queryPeta->whereHas('kelurahan', function ($q) use ($request) {
                $q->where('kecamatan_id', $request->kecamatan_id);
            });
        }
        if ($request->filled('kelurahan_id')) {
            $queryPeta->where('kelurahan_id', $request->kelurahan_id);
        }
        // Catatan: Pastikan kolom status_legalitas ada di tabel umkms
        if ($request->filled('status_nib')) {
             $queryPeta->where('status_nib', $request->status_nib);
        }
        
        $locations = $queryPeta->get();
        $sectors = Umkm::select('sektor_usaha')->distinct()->orderBy('sektor_usaha')->pluck('sektor_usaha');
        $kecamatans = Kecamatan::orderBy('nama_kecamatan')->get();

        // =====================================================================
        // BAGIAN 2: DATA UNTUK KARTU STATISTIK & GRAFIK RINGKASAN
        // =====================================================================
        
        // Statistik Total UMKM
        $totalUmkm = Umkm::count();

        // Statistik per Sektor
        $sektorData = Umkm::select('sektor_usaha', DB::raw('count(*) as total'))
                         ->groupBy('sektor_usaha')
                         ->pluck('total', 'sektor_usaha');

        // Statistik per Kecamatan
        $kecamatanData = Kecamatan::withCount(['kelurahans as total_umkm' => function ($query) {
                                $query->select(DB::raw('count(distinct umkms.id)'))
                                      ->join('umkms', 'kelurahans.id', '=', 'umkms.kelurahan_id');
                            }])
                            ->get()
                            ->pluck('total_umkm', 'nama_kecamatan');

        // Statistik Status NIB
        $nibData = Umkm::select('status_nib', DB::raw('count(*) as total'))
                      ->groupBy('status_nib')
                      ->pluck('total', 'status_nib');
        
        // Statistik Program Pembinaan Terpopuler
        $programData = Program::withCount('pesertas')
                              ->orderBy('pesertas_count', 'desc')
                              ->take(5) // Ambil 5 program terpopuler
                              ->get();

        // =====================================================================
        // BAGIAN 3: DATA UNTUK GRAFIK PERTUMBUHAN UMKM (12 BULAN TERAKHIR)
        // =====================================================================
        $growthData = Umkm::select(
                DB::raw('COUNT(id) as count'),
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month")
            )
            ->where('created_at', '>=', DB::raw('NOW() - INTERVAL 12 MONTH'))
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        $chartLabels = $growthData->pluck('month');
        $chartValues = $growthData->pluck('count');
        
        // =====================================================================
        // MENGIRIM SEMUA DATA KE VIEW
        // =====================================================================
        return view('dashboard', compact(
            'locations', 
            'sectors', 
            'kecamatans', 
            'chartLabels', 
            'chartValues',
            'totalUmkm',
            'sektorData',
            'kecamatanData',
            'nibData',
            'programData'
        ));
    }
}
