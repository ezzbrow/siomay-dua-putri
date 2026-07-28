<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAlamatLatLngToPesanan extends Migration
{
    public function up()
    {
        // Menambahkan kolom lat/lng ke tabel `pesanan` untuk menyimpan koordinat
        // lokasi dari peta Leaflet (checkout step "Diantar", §3.2.b). Hanya
        // digunakan untuk metode diantar; NULL untuk ambil_sendiri.
        //
        // Tipe DECIMAL(10,7) cukup untuk presisi koordinat GPS (mis. -0.8917000,
        // 119.8707000) — total 10 digit dengan 7 di belakang koma, range ±9999.9999999.
        $this->forge->addColumn('pesanan', [
            'alamat_lat' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,7',
                'null'       => true,
                'after'      => 'alamat',
            ],
            'alamat_lng' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,7',
                'null'       => true,
                'after'      => 'alamat_lat',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('pesanan', ['alamat_lat', 'alamat_lng']);
    }
}
