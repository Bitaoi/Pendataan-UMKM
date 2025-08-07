<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Umkm extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_usaha',
        'nama_pemilik',
        'alamat',
        'kontak',
        'sektor_usaha',
        'status_legalitas',
        'kecamatan',
        'latitude',
        'longitude',
        'path_dokumen'
    ];
}
