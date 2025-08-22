<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('program_umkm', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained('programs')->onDelete('cascade');
            $table->foreignId('umkm_id')->constrained('umkms')->onDelete('cascade');
            $table->timestamp('tanggal_pendaftaran')->useCurrent();
            $table->unique(['program_id', 'umkm_id']); // Pastikan satu UMKM tidak bisa daftar 2x di program yg sama
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('program_umkm');
    }
};