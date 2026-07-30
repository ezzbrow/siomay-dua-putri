<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Buat tabel `pesanan_acara` (F21 — Pesan Stand, eks-Pesanan Acara/Kegiatan).
 *
 * CATATAN: §9 CLAUDE.md sudah menyebut tabel ini sejak revisi 28 Juli 2026,
 * tapi tidak ada migration yang membuatnya. Migration ini melengkapi gap
 * tersebut. Schema final mencakup semua kolom untuk Pesan Stand:
 *  - Standar pesanan: pembeli_id, nama_pemesan, nomor_hp, jenis_acara,
 *    nama_acara, tanggal_acara, lokasi_acara, estimasi_porsi, catatan
 *  - Snapshot: subtotal, biaya_stand, total
 *  - Status 2 kolom (sesuai revisi): status_pembayaran, status_followup
 *  - Tracking: created_at, updated_at
 *
 * Tabel `transaksi.pesanan_acara_id` (FK ke tabel ini) dibuat di migration
 * terpisah (000006).
 */
class CreatePesananAcaraTable extends Migration
{
    public function up()
    {
        // CATATAN (fix 30 Juli 2026): file ini awalnya bernama "000006a" dan
        // dimaksudkan jalan SETELAH 000006 — tapi format itu tidak dikenali
        // regex MigrationRunner CI4 (hanya terima versi angka murni), jadi
        // file ini tidak pernah benar-benar jalan lewat `php spark migrate`.
        // Selain itu urutan itu memang salah: 000006 butuh tabel ini SUDAH ADA
        // untuk bikin FK ke sini, jadi migration ini harus jalan DULUAN.
        // Di-rename jadi "000005" supaya (a) match regex CI4, (b) jalan
        // sebelum 000006. Guard idempotency di bawah tetap dipertahankan.
        if ($this->db->query("SHOW TABLES LIKE 'pesanan_acara'")->getNumRows() > 0) {
            return;
        }

        $this->forge->addField([
            'id'                  => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'auto_increment' => true],
            'kode_booking'        => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => false],
            'pembeli_id'          => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'null' => false],
            'nama_pemesan'        => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => false],
            'nomor_hp'            => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => false],
            'jenis_acara'         => ['type' => 'ENUM', 'constraint' => ['ulang_tahun', 'pernikahan', 'acara_perusahaan', 'pembukaan_kantor', 'arisan', 'acara_keagamaan', 'acara_kedukaan', 'lainnya'], 'null' => false],
            'nama_acara'          => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => false],
            'tanggal_acara'       => ['type' => 'DATE', 'null' => false],
            'lokasi_acara'        => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => false],
            'estimasi_porsi'      => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'null' => true],
            'catatan'             => ['type' => 'TEXT', 'null' => true],
            'subtotal'            => ['type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 0.00, 'null' => false],
            'biaya_stand'         => ['type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 0.00, 'null' => false],
            'total'               => ['type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 0.00, 'null' => false],
            'status_pembayaran'   => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'pending', 'null' => false],
            'status_followup'     => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'baru', 'null' => false],
            'created_at'          => ['type' => 'DATETIME', 'null' => true],
            'updated_at'          => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('kode_booking');
        $this->forge->addForeignKey('pembeli_id', 'pembeli', 'id', '', 'RESTRICT');
        $this->forge->createTable('pesanan_acara');
    }

    public function down()
    {
        $this->forge->dropTable('pesanan_acara');
    }
}
