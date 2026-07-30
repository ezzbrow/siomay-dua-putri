<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Tambah kolom pendukung alur QRIS statis + konfirmasi manual admin:
 *  - pengaturan.qris_image   → path gambar QRIS statis yang di-upload admin
 *  - pesanan.status          → tambah nilai 'menunggu_konfirmasi' di ENUM
 *
 * Idempotent: semua perubahan dijaga agar aman dijalankan ulang.
 */
class AddQrisStatisSchema extends Migration
{
    private function columnExists(string $table, string $column): bool
    {
        $row = $this->db->query(
            "SELECT COUNT(*) AS c FROM INFORMATION_SCHEMA.COLUMNS
             WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ?",
            [$this->db->getDatabase(), $table, $column]
        )->getRowArray();
        return ((int) ($row['c'] ?? 0)) > 0;
    }

    public function up()
    {
        // 1. pengaturan.qris_image — path file QRIS statis
        if (! $this->columnExists('pengaturan', 'qris_image')) {
            $this->forge->addColumn('pengaturan', [
                'qris_image' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                    'default'    => null,
                    'after'      => 'jam_tutup',
                ],
            ]);
        }

        // 2. pesanan.status — tambah 'menunggu_konfirmasi' ke ENUM
        // MySQL ENUM ALTER: perlu tulis ulang seluruh set nilai
        $this->db->query(
            "ALTER TABLE pesanan
             MODIFY COLUMN status ENUM(
                 'pending',
                 'menunggu_konfirmasi',
                 'lunas',
                 'gagal',
                 'kedaluwarsa'
             ) NOT NULL DEFAULT 'pending'"
        );
    }

    public function down()
    {
        // Hapus qris_image
        if ($this->columnExists('pengaturan', 'qris_image')) {
            $this->forge->dropColumn('pengaturan', 'qris_image');
        }

        // Kembalikan pesanan.status ke ENUM tanpa menunggu_konfirmasi
        $this->db->query(
            "ALTER TABLE pesanan
             MODIFY COLUMN status ENUM(
                 'pending',
                 'lunas',
                 'gagal',
                 'kedaluwarsa'
             ) NOT NULL DEFAULT 'pending'"
        );
    }
}
