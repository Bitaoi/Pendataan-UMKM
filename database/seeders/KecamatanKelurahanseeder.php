<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class KecamatanKelurahanseeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // mematikan foreign key check untuk menghindari error saat truncate
        Schema::disableForeignKeyConstraints();

        //mengosongkan tabel kelurahan dan kecamatan
        DB::table('kelurahans')->truncate();
        DB::table('kecamatan')->truncate();

        //menghidupkan ulang foreign key check
        Schema:: enableForeignKeyConstraints();

        //mendefinisikan dan memasukkan data kecamatan
        $kecamatans = [
            ['id' => 1, 'nama_kecamatan' => 'Mojoroto', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 1, 'nama_kecamatan' => 'Kota', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 1, 'nama_kecamatan' => 'Pesantren', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('kecamatans')->insert($kecamatans);

        //mendefinisikan dan memasukkan data kelurahan
        $kelurahans = [
            // Kecamatan Mojoroto (id: 1)
            ['kecamatan_id' => 1, 'nama_kelurahan' => 'Bandar Kidul', 'created_at' => now(), 'updated_at' => now()],
            ['kecamatan_id' => 1, 'nama_kelurahan' => 'Bandar Lor', 'created_at' => now(), 'updated_at' => now()],
            ['kecamatan_id' => 1, 'nama_kelurahan' => 'Banjaran', 'created_at' => now(), 'updated_at' => now()],
            ['kecamatan_id' => 1, 'nama_kelurahan' => 'Bujel', 'created_at' => now(), 'updated_at' => now()],
            ['kecamatan_id' => 1, 'nama_kelurahan' => 'Campurejo', 'created_at' => now(), 'updated_at' => now()],
            ['kecamatan_id' => 1, 'nama_kelurahan' => 'Dermo', 'created_at' => now(), 'updated_at' => now()],
            ['kecamatan_id' => 1, 'nama_kelurahan' => 'Gayam', 'created_at' => now(), 'updated_at' => now()],
            ['kecamatan_id' => 1, 'nama_kelurahan' => 'Lirboyo', 'created_at' => now(), 'updated_at' => now()],
            ['kecamatan_id' => 1, 'nama_kelurahan' => 'Mojoroto', 'created_at' => now(), 'updated_at' => now()],
            ['kecamatan_id' => 1, 'nama_kelurahan' => 'Mrican', 'created_at' => now(), 'updated_at' => now()],
            ['kecamatan_id' => 1, 'nama_kelurahan' => 'Ngampel', 'created_at' => now(), 'updated_at' => now()],
            ['kecamatan_id' => 1, 'nama_kelurahan' => 'Pojok', 'created_at' => now(), 'updated_at' => now()],
            ['kecamatan_id' => 1, 'nama_kelurahan' => 'Sukorame', 'created_at' => now(), 'updated_at' => now()],
            ['kecamatan_id' => 1, 'nama_kelurahan' => 'Tamanan', 'created_at' => now(), 'updated_at' => now()],

            // Kecamatan Kota (id: 2)
            ['kecamatan_id' => 2, 'nama_kelurahan' => 'Baler Baleagung', 'created_at' => now(), 'updated_at' => now()],
            ['kecamatan_id' => 2, 'nama_kelurahan' => 'Balowerti', 'created_at' => now(), 'updated_at' => now()],
            ['kecamatan_id' => 2, 'nama_kelurahan' => 'Banjarmelati', 'created_at' => now(), 'updated_at' => now()],
            ['kecamatan_id' => 2, 'nama_kelurahan' => 'Dandangan', 'created_at' => now(), 'updated_at' => now()],
            ['kecamatan_id' => 2, 'nama_kelurahan' => 'Jagalan', 'created_at' => now(), 'updated_at' => now()],
            ['kecamatan_id' => 2, 'nama_kelurahan' => 'Kaliombo', 'created_at' => now(), 'updated_at' => now()],
            ['kecamatan_id' => 2, 'nama_kelurahan' => 'Kampungdalem', 'created_at' => now(), 'updated_at' => now()],
            ['kecamatan_id' => 2, 'nama_kelurahan' => 'Kemasan', 'created_at' => now(), 'updated_at' => now()],
            ['kecamatan_id' => 2, 'nama_kelurahan' => 'Manisrenggo', 'created_at' => now(), 'updated_at' => now()],
            ['kecamatan_id' => 2, 'nama_kelurahan' => 'Ngronggo', 'created_at' => now(), 'updated_at' => now()],
            ['kecamatan_id' => 2, 'nama_kelurahan' => 'Pakelan', 'created_at' => now(), 'updated_at' => now()],
            ['kecamatan_id' => 2, 'nama_kelurahan' => 'Pocanan', 'created_at' => now(), 'updated_at' => now()],
            ['kecamatan_id' => 2, 'nama_kelurahan' => 'Rejomulyo', 'created_at' => now(), 'updated_at' => now()],
            ['kecamatan_id' => 2, 'nama_kelurahan' => 'Ringinanom', 'created_at' => now(), 'updated_at' => now()],
            ['kecamatan_id' => 2, 'nama_kelurahan' => 'Semampir', 'created_at' => now(), 'updated_at' => now()],
            ['kecamatan_id' => 2, 'nama_kelurahan' => 'Setonogedong', 'created_at' => now(), 'updated_at' => now()],
            ['kecamatan_id' => 2, 'nama_kelurahan' => 'Setonopande', 'created_at' => now(), 'updated_at' => now()],

            // Kecamatan Pesantren (id: 3)
            ['kecamatan_id' => 3, 'nama_kelurahan' => 'Banaran', 'created_at' => now(), 'updated_at' => now()],
            ['kecamatan_id' => 3, 'nama_kelurahan' => 'Bangsal', 'created_at' => now(), 'updated_at' => now()],
            ['kecamatan_id' => 3, 'nama_kelurahan' => 'Betet', 'created_at' => now(), 'updated_at' => now()],
            ['kecamatan_id' => 3, 'nama_kelurahan' => 'Bawang', 'created_at' => now(), 'updated_at' => now()],
            ['kecamatan_id' => 3, 'nama_kelurahan' => 'Blabak', 'created_at' => now(), 'updated_at' => now()],
            ['kecamatan_id' => 3, 'nama_kelurahan' => 'Burengan', 'created_at' => now(), 'updated_at' => now()],
            ['kecamatan_id' => 3, 'nama_kelurahan' => 'Jamsaren', 'created_at' => now(), 'updated_at' => now()],
            ['kecamatan_id' => 3, 'nama_kelurahan' => 'Ketami', 'created_at' => now(), 'updated_at' => now()],
            ['kecamatan_id' => 3, 'nama_kelurahan' => 'Ngletih', 'created_at' => now(), 'updated_at' => now()],
            ['kecamatan_id' => 3, 'nama_kelurahan' => 'Pakunden', 'created_at' => now(), 'updated_at' => now()],
            ['kecamatan_id' => 3, 'nama_kelurahan' => 'Pesantren', 'created_at' => now(), 'updated_at' => now()],
            ['kecamatan_id' => 3, 'nama_kelurahan' => 'Singonegaran', 'created_at' => now(), 'updated_at' => now()],
            ['kecamatan_id' => 3, 'nama_kelurahan' => 'Tempurejo', 'created_at' => now(), 'updated_at' => now()],
            ['kecamatan_id' => 3, 'nama_kelurahan' => 'Tinalan', 'created_at' => now(), 'updated_at' => now()],
            ['kecamatan_id' => 3, 'nama_kelurahan' => 'Tosaren', 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('kelurahans')->insert($kelurahans);

    }
}
