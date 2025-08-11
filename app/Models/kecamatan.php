<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class kecamatan extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama_kecamatan',
    ];

    /**
     * Mendefinisikan relasi bahwa satu Kecamatan memiliki banyak Kelurahan.
     */
    public function kelurahans()
    {
        return $this->hasMany(Kelurahan::class);
    }
}
