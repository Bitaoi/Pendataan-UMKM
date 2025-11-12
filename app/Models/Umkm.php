<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Umkm extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama_usaha',
        'nama_pemilik',
        'kelurahan_id',
        'alamat_lengkap',
        'nomor_telepon',
        'kategori_umkm', // <-- TAMBAHKAN INI
        'status_halal', // <-- TAMBAHKAN INI
        'sektor_usaha',
        'status_nib',
        'nomor_kbli',
        'dokumen_legalitas_path',
        'latitude',
        'longitude',
    ];

    /**
     * Relasi ke model Kelurahan.
     */
    public function kelurahan()
    {
        return $this->belongsTo(Kelurahan::class);
    }

    /**
     * Relasi ke program-program yang diikuti oleh UMKM ini.
     */
    public function programs()
    {
        return $this->belongsToMany(Program::class, 'program_umkm');
    }
}