<?php

namespace App\Http\Controllers;

use App\Models\Umkm;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // ... (Bagian Query Filter di atas biarkan tetap sama) ...
        
        $queryPeta = Umkm::query()
                        ->whereNotNull('latitude')
                        ->whereNotNull('longitude')
                        ->where('latitude', '!=', '')
                        ->where('longitude', '!=', '');

        // ... (Logika Filter Sektor, Kecamatan, NIB biarkan tetap sama) ...
        if ($request->filled('sektor_usaha')) {
            $queryPeta->where('sektor_usaha', $request->sektor_usaha);
        }
        $selectedKecamatanId = $request->input('kecamatan_id');
        $selectedKelurahanId = $request->input('kelurahan_id');

        if ($selectedKelurahanId) {
            $queryPeta->where('kelurahan_id', $selectedKelurahanId);
        } elseif ($selectedKecamatanId) {
            $queryPeta->whereHas('kelurahan', function ($q) use ($selectedKecamatanId) {
                $q->where('kecamatan_id', $selectedKecamatanId);
            });
        }

        if ($request->filled('status_legalitas')) {
            if ($request->status_legalitas == 'legal') {
                $queryPeta->where(function ($q) {
                    $q->whereNotNull('status_nib')->where('status_nib', '!=', '');
                });
            } elseif ($request->status_legalitas == 'illegal') {
                $queryPeta->where(function ($q) {
                    $q->whereNull('status_nib')->orWhere('status_nib', '=', '');
                });
            }
        }
        
        // ▼▼▼ PERBAIKAN UTAMA DISINI ▼▼▼
        // Kita tambahkan kolom detail ke dalam get()
        $locations = $queryPeta->get([
            'id', 
            'nama_usaha', 
            'nama_pemilik',      // Tambah ini
            'alamat_lengkap',    // Tambah ini
            'sektor_usaha',      // Tambah ini
            'kategori_umkm',     // Tambah ini
            'status_halal',      // Tambah ini
            'latitude', 
            'longitude'
        ]);
        // ▲▲▲ SELESAI PERBAIKAN ▲▲▲

        // ... (Sisa kode ke bawah biarkan tetap sama) ...
        $sectors = Umkm::select('sektor_usaha')->whereNotNull('sektor_usaha')->where('sektor_usaha', '!=', '')->distinct()->orderBy('sektor_usaha')->pluck('sektor_usaha');
        $kecamatans = Kecamatan::orderBy('nama_kecamatan')->get();
        $totalUmkm = Umkm::count();
        
        // Statistik
        $nibData = Umkm::select(DB::raw('CASE WHEN status_nib IS NULL OR status_nib = "" OR status_nib = "Belum Ada" THEN "Tanpa NIB" ELSE "Dengan NIB" END as status_label'), DB::raw('count(*) as total'))
                    ->groupBy('status_label')->pluck('total', 'status_label');

        $programData = Program::withCount('pesertas')->orderBy('pesertas_count', 'desc')->take(5)->get();
                                
        // Grafik Pertumbuhan
        $startDate = Carbon::now()->subMonths(11)->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();
        $growthDataRaw = Umkm::select(DB::raw('COUNT(id) as count'), DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month_year"))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('month_year')->orderBy('month_year', 'asc')->pluck('count', 'month_year');

        $chartData = [];
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $monthKey = $currentDate->format('Y-m');
            $chartData[$monthKey] = $growthDataRaw->get($monthKey, 0);
            $currentDate->addMonth();
        }

        $chartLabels = array_keys($chartData);
        $chartValues = array_values($chartData);
        
        return view('dashboard', compact(
            'locations', 'sectors', 'kecamatans', 'chartLabels', 'chartValues', 'programData', 'selectedKecamatanId'
        ));
    }
}