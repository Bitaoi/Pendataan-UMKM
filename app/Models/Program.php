<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    // Relasi ke UMKM yang menjadi peserta
    public function pesertas()
    {
        return $this->belongsToMany(Umkm::class, 'program_umkm');
    }

    // Relasi ke log progres
    public function logs()
    {
        return $this->hasMany(ProgramLog::class);
    }
}