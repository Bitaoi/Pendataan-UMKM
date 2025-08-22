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
        'alamat_lengkap', // <-- Memastikan kolom ini diizinkan
        'nomor_telepon',  // <-- Memastikan kolom ini diizinkan
        'sektor_usaha',
        'status_nib',     // <-- Memastikan kolom ini diizinkan
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
    
    // Jika Anda masih memiliki relasi ke kecamatan di model ini,
    // sebaiknya dihapus karena relasi yang benar adalah melalui Kelurahan.
    // public function kecamatan() { ... }
}
