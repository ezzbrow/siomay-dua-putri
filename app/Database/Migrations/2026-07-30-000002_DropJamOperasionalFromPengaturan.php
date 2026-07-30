<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Hapus fitur jam operasional total (keputusan 30 Juli 2026) - grey-out
 * etalase sekarang cuma berdasarkan produk.status_aktif, tidak lagi
 * dikombinasikan dengan jam buka/tutup toko. Lihat juga penghapusan
 * ProductAvailability helper dan semua pemanggilnya di controller/view.
 */
class DropJamOperasionalFromPengaturan extends Migration
{
    public function up()
    {
        if (! $this->db->fieldExists('jam_buka', 'pengaturan')) {
            return;
        }

        $this->forge->dropColumn('pengaturan', ['jam_buka', 'jam_tutup']);
    }

    public function down()
    {
        $this->forge->addColumn('pengaturan', [
            'jam_buka' => [
                'type' => 'TIME',
                'null' => true,
            ],
            'jam_tutup' => [
                'type' => 'TIME',
                'null' => true,
            ],
        ]);
    }
}
