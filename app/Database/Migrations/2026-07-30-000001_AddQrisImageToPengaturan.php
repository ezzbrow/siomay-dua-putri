<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Tambah kolom `pengaturan.qris_image` — nama file gambar QRIS statis yang
 * diupload admin (menggantikan QRIS dinamis dari Midtrans yang dihapus).
 * Nullable karena admin belum tentu langsung upload begitu migration ini
 * jalan — Checkout::pembayaran() harus handle kondisi NULL dengan pesan
 * "QRIS belum diupload admin", bukan asumsikan selalu ada.
 */
class AddQrisImageToPengaturan extends Migration
{
    public function up()
    {
        if ($this->db->fieldExists('qris_image', 'pengaturan')) {
            return;
        }

        $this->forge->addColumn('pengaturan', [
            'qris_image' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'biaya_stand',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('pengaturan', 'qris_image');
    }
}
