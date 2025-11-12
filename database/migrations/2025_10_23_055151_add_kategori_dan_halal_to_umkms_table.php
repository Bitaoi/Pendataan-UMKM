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
        Schema::table('umkms', function (Blueprint $table) {
            // Pastikan kolom 'kategori_umkm' sudah ada
            // Tambahkan kolom 'status_halal' setelahnya
            $table->string('status_halal')->after('kategori_umkm')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('umkms', function (Blueprint $table) {
            $table->dropColumn('status_halal');
        });
    }
};