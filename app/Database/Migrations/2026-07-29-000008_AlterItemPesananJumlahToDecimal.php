<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Ubah `item_pesanan.jumlah` dari INT(10) UNSIGNED ke DECIMAL(8,2) UNSIGNED.
 *
 * LATAR BELAKANG: schema Pesan Antar (existing) pakai INT karena semua
 * produk lama adalah per-PCS dengan step 1. Setelah ada produk per-KG
 * (Siomay Kukus, Tahu Kukus) dengan step 0.5, kolom jumlah harus
 * dukung desimal.
 *
 * DATA EXISTING (per pre-check sebelum migration ini):
 *   - 6 row total, max 50, min 15 — semua integer
 *   - Auto-convert INT → DECIMAL: lossless (50 → 50.00)
 *
 * ROLLBACK: `ALTER TABLE item_pesanan MODIFY COLUMN jumlah INT(10) UNSIGNED NOT NULL;`
 *   - Risk: jika ada row desimal (0.5), MySQL truncate ke 0 atau 1.
 *   - Saat ini 0 row kg ada (Pesan Stand belum dibangun) → rollback aman.
 */
class AlterItemPesananJumlahToDecimal extends Migration
{
    public function up()
    {
        // MySQL: ALTER TABLE ... MODIFY COLUMN preserves integer values exactly
        // when converting INT to DECIMAL. 50 (INT) → 50.00 (DECIMAL(8,2)).
        $this->db->query('ALTER TABLE item_pesanan MODIFY COLUMN jumlah DECIMAL(8,2) UNSIGNED NOT NULL');
    }

    public function down()
    {
        $this->db->query('ALTER TABLE item_pesanan MODIFY COLUMN jumlah INT(10) UNSIGNED NOT NULL');
    }
}
