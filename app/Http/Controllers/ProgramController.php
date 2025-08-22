<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\Umkm;
use App\Models\ProgramLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProgramController extends Controller
{
    // Menampilkan daftar semua program
    public function index()
    {
        $programs = Program::withCount('pesertas')->latest()->paginate(10);
        return view('programs.index', compact('programs'));
    }

    // Menampilkan form untuk membuat program baru
    public function create()
    {
        return view('programs.create');
    }

    // Menyimpan program baru
    public function store(Request $request)
    {
        $request->validate([
            'nama_program' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'kuota_peserta' => 'required|integer|min:1',
        ]);

        Program::create($request->all());

        return redirect()->route('programs.index')->with('success', 'Program berhasil dibuat.');
    }

    // Menampilkan detail program, daftar peserta, dan log
    public function show(Program $program)
    {
        $program->load('pesertas', 'logs.user', 'logs.umkm');
        $umkmsNotInProgram = Umkm::whereDoesntHave('programs', function ($query) use ($program) {
            $query->where('program_id', $program->id);
        })->get();

        return view('programs.show', compact('program', 'umkmsNotInProgram'));
    }

    // (Opsional) Menampilkan form edit program
    public function edit(Program $program)
    {
        return view('programs.edit', compact('program'));
    }

    // (Opsional) Menyimpan perubahan pada program
    public function update(Request $request, Program $program)
    {
        // ... (logika validasi dan update)
        return redirect()->route('programs.index')->with('success', 'Program berhasil diperbarui.');
    }

    // Menghapus program
    public function destroy(Program $program)
    {
        $program->delete();
        return redirect()->route('programs.index')->with('success', 'Program berhasil dihapus.');
    }

    // --- METODE KHUSUS UNTUK MANAJEMEN PESERTA & LOG ---

    // Menambahkan UMKM sebagai peserta program
    public function addPeserta(Request $request, Program $program)
    {
        $request->validate(['umkm_id' => 'required|exists:umkms,id']);

        if ($program->pesertas()->count() >= $program->kuota_peserta) {
            return back()->with('error', 'Kuota peserta sudah penuh.');
        }

        $program->pesertas()->attach($request->umkm_id);
        return back()->with('success', 'Peserta berhasil ditambahkan.');
    }

    // Menghapus UMKM dari kepesertaan program
    public function removePeserta(Program $program, Umkm $umkm)
    {
        $program->pesertas()->detach($umkm->id);
        return back()->with('success', 'Peserta berhasil dihapus.');
    }

    // Menambahkan log progres untuk peserta
    public function addLog(Request $request, Program $program)
    {
        $request->validate([
            'umkm_id' => 'required|exists:umkms,id',
            'tanggal_log' => 'required|date',
            'deskripsi_progres' => 'required|string',
        ]);

        ProgramLog::create([
            'program_id' => $program->id,
            'umkm_id' => $request->umkm_id,
            'tanggal_log' => $request->tanggal_log,
            'deskripsi_progres' => $request->deskripsi_progres,
            'dicatat_oleh_user_id' => Auth::id(),
        ]);

        return back()->with('success', 'Log progres berhasil ditambahkan.');
    }
}