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
            $table->text('alamat');
            $table->string('kontak');
            $table->string('sektor_usaha');
            $table->string('status_legalitas');
            
            // --- INI BAGIAN YANG DIPERBAIKI ---
            // Menghapus $table->string('kecamatan');
            // dan menggantinya dengan relasi yang benar.
            $table->foreignId('kecamatan_id')->constrained('kecamatans')->onDelete('cascade');
            $table->foreignId('kelurahan_id')->constrained('kelurahans')->onDelete('cascade');
            // ------------------------------------

            // Mempertahankan kolom latitude dan longitude Anda
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 11, 7)->nullable();
            
            $table->string('path_dokumen')->nullable();
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
