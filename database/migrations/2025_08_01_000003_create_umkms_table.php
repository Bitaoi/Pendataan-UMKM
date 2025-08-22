<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('umkms', function (Blueprint $table) {
            $table->id();
            $table->string('nama_usaha');
            $table->string('nama_pemilik');
            
            // Menggunakan foreign key yang benar ke tabel kelurahans
            $table->foreignId('kelurahan_id')->constrained('kelurahans')->onDelete('cascade');
            
            $table->text('alamat_lengkap');
            $table->string('nomor_telepon');
            $table->string('sektor_usaha');
            
            // Menggunakan nama kolom yang benar: 'status_nib'
            $table->enum('status_nib', ['Sudah Ada', 'Belum Ada', 'Sedang Proses']);
            
            $table->string('nomor_kbli')->nullable();
            $table->string('dokumen_legalitas_path')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 11, 7)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('umkms');
    }
};
