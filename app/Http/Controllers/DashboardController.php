<?php

namespace App\Http\Controllers;

use App\Models\Umkm;
use App\Models\Kecamatan;
use App\Models\Kelurahan; // Pastikan Kelurahan di-import
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon; // Import Carbon untuk manipulasi tanggal

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
        
        // Mulai query dasar untuk peta
        $queryPeta = Umkm::query()
                        ->whereNotNull('latitude')
                        ->whereNotNull('longitude')
                        ->where('latitude', '!=', '') // Tambahan keamanan data
                        ->where('longitude', '!=', ''); // Tambahan keamanan data

        // Filter Sektor Usaha
        if ($request->filled('sektor_usaha')) {
            $queryPeta->where('sektor_usaha', $request->sektor_usaha);
        }

        // ▼▼▼ PERBAIKAN LOGIKA FILTER KECAMATAN & KELURAHAN (Request 3) ▼▼▼
        $selectedKecamatanId = $request->input('kecamatan_id');
        $selectedKelurahanId = $request->input('kelurahan_id');

        if ($selectedKelurahanId) {
            // Jika kelurahan dipilih, langsung filter berdasarkan kelurahan_id.
            // Ini adalah filter yang paling spesifik.
            $queryPeta->where('kelurahan_id', $selectedKelurahanId);
        } elseif ($selectedKecamatanId) {
            // Jika HANYA kecamatan yang dipilih (kelurahan tidak),
            // filter berdasarkan semua kelurahan di dalam kecamatan itu.
            $queryPeta->whereHas('kelurahan', function ($q) use ($selectedKecamatanId) {
                $q->where('kecamatan_id', $selectedKecamatanId);
            });
        }
        // ▲▲▲ AKHIR PERBAIKAN LOGIKA FILTER ▲▲▲

        // Filter Status NIB (dari 'status_legalitas' form)
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
        
        // Ambil data lokasi (hanya kolom yang diperlukan untuk peta)
        $locations = $queryPeta->get(['id', 'nama_usaha', 'latitude', 'longitude']);

        // Data untuk dropdown filter
        $sectors = Umkm::select('sektor_usaha')->whereNotNull('sektor_usaha')->where('sektor_usaha', '!=', '')->distinct()->orderBy('sektor_usaha')->pluck('sektor_usaha');
        $kecamatans = Kecamatan::orderBy('nama_kecamatan')->get();

        // =====================================================================
        // BAGIAN 2: DATA UNTUK STATISTIK RINGKASAN
        // =====================================================================
        
        $totalUmkm = Umkm::count();

        // Statistik Status NIB
        $nibData = Umkm::select(DB::raw('CASE WHEN status_nib IS NULL OR status_nib = "" OR status_nib = "Belum Ada" THEN "Tanpa NIB" ELSE "Dengan NIB" END as status_label'), DB::raw('count(*) as total'))
                    ->groupBy('status_label')
                    ->pluck('total', 'status_label');

        // Statistik Program Pembinaan Terpopuler
        $programData = Program::withCount('pesertas') // Asumsi relasi 'pesertas' ada di model Program
                                ->orderBy('pesertas_count', 'desc')
                                ->take(5)
                                ->get();
                                
        // =====================================================================
        // BAGIAN 3: DATA UNTUK GRAFIK PERTUMBUHAN UMKM (12 BULAN TERAKHIR)
        // =====================================================================
        
        // Tentukan rentang 12 bulan (dari 11 bulan lalu s/d bulan ini)
        $startDate = Carbon::now()->subMonths(11)->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        // Ambil data dari DB
        $growthDataRaw = Umkm::select(
                DB::raw('COUNT(id) as count'),
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month_year")
            )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('month_year')
            ->orderBy('month_year', 'asc')
            ->pluck('count', 'month_year'); // Hasil: ['2024-01' => 5, '2024-03' => 2]

        // Buat array 12 bulan lengkap dengan nilai default 0
        $chartData = [];
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $monthKey = $currentDate->format('Y-m');
            $chartData[$monthKey] = $growthDataRaw->get($monthKey, 0); // Ambil data dari DB, atau 0 jika tidak ada
            $currentDate->addMonth();
        }

        $chartLabels = array_keys($chartData);
        $chartValues = array_values($chartData);
        
        // =====================================================================
        // MENGIRIM SEMUA DATA KE VIEW
        // =====================================================================
        return view('dashboard', compact(
            'locations', 
            'sectors', 
            'kecamatans', 
            'chartLabels', 
            'chartValues',
            // 'totalUmkm', // Anda belum menggunakan ini di blade
            // 'nibData', // Anda belum menggunakan ini di blade
            'programData'
            // Kirim ID terpilih agar filter bisa menampilkan kelurahan yang benar
            ,'selectedKecamatanId' 
        ));
    }
}