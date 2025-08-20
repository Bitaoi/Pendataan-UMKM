<?php

namespace App\Http\Controllers;

use App\Models\Umkm;
use App\Models\Kecamatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
// use Carbon\Carbon; // Carbon tidak lagi digunakan

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard dengan data untuk peta dan grafik.
     */
    public function index(Request $request)
    {
        // --- LOGIKA UNTUK PETA (TETAP SAMA) ---
        $query = Umkm::query()->whereNotNull('latitude')->whereNotNull('longitude');

        if ($request->filled('sektor_usaha')) {
            $query->where('sektor_usaha', $request->sektor_usaha);
        }
        if ($request->filled('kecamatan_id')) {
            $query->where('kecamatan_id', $request->kecamatan_id);
        }
        if ($request->filled('kelurahan_id')) {
            $query->where('kelurahan_id', $request->kelurahan_id);
        }
        if ($request->filled('status_legalitas')) {
            if ($request->status_legalitas === 'legal') {
                $query->where('status_legalitas', '!=', 'Illegal')->whereNotNull('status_legalitas');
            } elseif ($request->status_legalitas === 'illegal') {
                $query->where(function ($q) {
                    $q->where('status_legalitas', 'Illegal')->orWhereNull('status_legalitas');
                });
            }
        }
        $locations = $query->get();
        
        $sectors = Umkm::select('sektor_usaha')->distinct()->orderBy('sektor_usaha')->pluck('sektor_usaha');
        $kecamatans = Kecamatan::orderBy('nama_kecamatan')->get();

        // --- LOGIKA BARU UNTUK GRAFIK PERTUMBUHAN ---
        $growthData = Umkm::select(
                DB::raw('COUNT(id) as count'),
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month")
            )
            // Mengganti Carbon dengan fungsi SQL bawaan
            ->where('created_at', '>=', DB::raw('NOW() - INTERVAL 12 MONTH'))
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        $chartLabels = $growthData->pluck('month');
        $chartValues = $growthData->pluck('count');
        // --- AKHIR LOGIKA BARU ---

        // Kirim semua data yang dibutuhkan ke view
        return view('dashboard', compact('locations', 'sectors', 'kecamatans', 'chartLabels', 'chartValues'));
    }
}
