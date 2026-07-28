<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Tabel baru `item_pesanan_acara` — mirror dari `item_pesanan` tapi untuk
 * Pesan Stand (F21).
 *
 * Tiap row adalah 1 produk yang dipilih di Pesan Stand:
 *  - Snapshot harga_satuan saat booking (bukan referensi live ke produk.harga)
 *  - jumlah DECIMAL(8,2) untuk konsistensi dengan item_pesanan (kg + pcs)
 *
 * CATATAN: Pesan Stand pakai produk dari tabel `produk` yang sama (shared
 * dengan Pesan Antar). TIDAK ada tabel `produk_acara` terpisah — pembeda
 * via flag `produk.tampil_di_pesan_stand`. Harga satuan per produk BARU
 * untuk Pesan Stand = row terpisah di `produk` (per revisi user).
 */
class CreateItemPesananAcaraTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                       => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'auto_increment' => true],
            'pesanan_acara_id'         => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'null' => false],
            'produk_id'                => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'null' => false],
            'jumlah'                   => ['type' => 'DECIMAL', 'constraint' => '8,2', 'null' => false],
            'harga_satuan_snapshot'    => ['type' => 'DECIMAL', 'constraint' => '12,2', 'null' => false],
            'subtotal_item'            => ['type' => 'DECIMAL', 'constraint' => '12,2', 'null' => false],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('pesanan_acara_id', 'pesanan_acara', 'id', '', 'CASCADE');
        $this->forge->addForeignKey('produk_id', 'produk', 'id', '', 'RESTRICT');
        $this->forge->createTable('item_pesanan_acara');

        // Index performa untuk lookup by pesanan_acara_id (kebanyakan query)
        $this->db->query('CREATE INDEX item_pesanan_acara_pesanan_acara_id_idx ON item_pesanan_acara (pesanan_acara_id)');
    }

    public function down()
    {
        $this->forge->dropTable('item_pesanan_acara');
    }
}
