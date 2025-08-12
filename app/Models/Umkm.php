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
        'alamat',
        'kontak',
        'sektor_usaha',
        'status_legalitas',
        'kecamatan_id',     // <-- INI YANG PENTING
        'kelurahan_id',     // <-- INI YANG PENTING
        'latitude',         // <-- Ditambahkan untuk masa depan
        'longitude',        // <-- Ditambahkan untuk masa depan
        'path_dokumen',
    ];

    /**
     * Mendefinisikan relasi bahwa satu UMKM dimiliki oleh satu Kecamatan.
     */
    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class);
    }

    /**
     * Mendefinisikan relasi bahwa satu UMKM dimiliki oleh satu Kelurahan.
     */
    public function kelurahan()
    {
        return $this->belongsTo(Kelurahan::class);
    }
}
