<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Skema tambahan untuk Pesan Stand (F21) + dukung produk per-KG (Pesan Antar).
 *
 * Revisi 28 Juli 2026 v3 (per rencana disetujui user):
 *  - produk.satuan ENUM('kg','pcs')  → step qty & hitung harga beda
 *  - produk.step_qty & min_qty       → konfigurasi kelipatan
 *  - produk.tampil_di_pesan_antar & tampil_di_pesan_stand → visibility per channel
 *  - pengaturan.biaya_stand          → admin-config fee booking stand
 *  - pesanan_acara: refactor jadi punya transaksi + dual status (pembayaran + followup)
 *  - transaksi.pesanan_acara_id + CHECK XOR (1-to-1 dengan pesanan ATAU pesanan_acara)
 *
 * CATATAN: tidak ada kode existing yang query `transaksi` table (sudah diaudit
 * sebelum migration ini dijalankan). Step 3b (Midtrans integration) akan query
 * polymorphic dengan `WHERE pesanan_id = ? OR pesanan_acara_id = ?`.
 *
 * === CATATAN AUDIT UNTUK REVIEWER MIGRATION HISTORY ===
 * Riwayat lama di bagian ini (sebelum fix 30 Juli 2026) mengira urutan file
 * "000006 lalu 000006a" itu jalan dan hanya soal idempotency. Ternyata akar
 * masalahnya lebih dalam: nama file "000006a" tidak match regex versi CI4
 * (lihat catatan di 2026-07-29-000005_CreatePesananAcaraTable.php), jadi
 * file itu TIDAK PERNAH jalan sama sekali lewat `php spark migrate` — dan
 * urutannya memang salah (migration ini butuh tabel pesanan_acara SUDAH ADA
 * sebelum baris ALTER TABLE transaksi ADD CONSTRAINT ... FK di bawah).
 *
 * Fix 30 Juli 2026: file pembuat tabel pesanan_acara di-rename jadi
 * "000005" (jalan sebelum migration ini). Guard idempotency di bawah
 * (columnExists check) tetap dipertahankan supaya aman di-re-run di
 * database yang skemanya sudah pernah ditambal manual sebelumnya.
 */
class AddPesanStandAndKgSchema extends Migration
{
    /**
     * Helper: cek apakah kolom sudah ada di tabel (idempotency guard).
     * Return true kalau kolom ada, false kalau belum.
     */
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
        // ============================================================
        // 1. produk: tambah kolom satuan, step_qty, min_qty, visibility
        // ============================================================
        // CATATAN: migration ini dijalankan setelah 000006a, 000007, 000008
        // (per pengurutan timestamp). Kalau dijalankan ulang (rerun), skip step
        // yang sudah selesai via cek INFORMATION_SCHEMA — agar tidak duplicate
        // column error.
        if ($this->columnExists('produk', 'satuan')) {
            // Kolom sudah ada — skip seluruh up() dan catat sebagai selesai
            return;
        }

        $this->forge->addColumn('produk', [
            'satuan' => [
                'type'       => 'ENUM',
                'constraint' => ['kg', 'pcs'],
                'default'    => 'pcs',
                'null'       => false,
                'after'      => 'harga',
            ],
            'step_qty' => [
                'type'       => 'DECIMAL',
                'constraint' => '8,2',
                'default'    => 1.00,
                'null'       => false,
                'after'      => 'satuan',
            ],
            'min_qty' => [
                'type'       => 'DECIMAL',
                'constraint' => '8,2',
                'default'    => 1.00,
                'null'       => false,
                'after'      => 'step_qty',
            ],
            'tampil_di_pesan_antar' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
                'null'       => false,
                'after'      => 'min_qty',
            ],
            'tampil_di_pesan_stand' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'null'       => false,
                'after'      => 'tampil_di_pesan_antar',
            ],
        ]);

        // CHECK constraint: untuk produk dengan nama/model yang overlap (Siomay,
        // Tahu, Lumpia), harga/satuan beda per channel — flag harus exclusive.
        // Per revisi: "tiap row cuma boleh punya salah satu true, kecuali"
        // (kecuali ke depan ada produk bener-bener sama harga & satuan di kedua
        // channel — untuk skenario Siomay/Tahu/Lumpia yang harga beda, ini gak
        // berlaku). Aplikasi code yang INSERT produk bertanggung jawab enforce
        // — DB CHECK tidak dipakai untuk fleksibilitas ke depan.
        // (TIDAK menambah CHECK constraint di level DB, hanya di aplikasi.)

        // ============================================================
        // 2. pengaturan: tambah biaya_stand
        // ============================================================
        $this->forge->addColumn('pengaturan', [
            'biaya_stand' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => 0.00,
                'null'       => false,
                'after'      => 'jam_tutup',
            ],
        ]);

        // ============================================================
        // 3. pesanan_acara: SKIP — sudah dibuat lengkap oleh 000006a
        // ============================================================
        // (Schema final pesanan_acara dengan semua kolom revisi sudah ada
        // setelah migration 000006a dijalankan. Tidak ada ALTER tambahan di sini.)

        // ============================================================
        // 4. transaksi: tambah pesanan_acara_id + CHECK XOR constraint
        // ============================================================
        $this->forge->addColumn('transaksi', [
            'pesanan_acara_id' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'pesanan_id',
            ],
        ]);

        // ALTER pesanan_id jadi NULLable
        $this->db->query('ALTER TABLE transaksi MODIFY COLUMN pesanan_id INT(10) UNSIGNED NULL');

        // Index untuk pesanan_acara_id
        $this->db->query('CREATE INDEX transaksi_pesanan_acara_id_idx ON transaksi (pesanan_acara_id)');

        // FK ke pesanan_acara — ON DELETE RESTRICT (konsisten dengan pola `pembeli` di
        // pesanan_acara.pembeli_id, lihat migration 000002). Tidak boleh hapus
        // pesanan_acara kalau ada transaksi tercatat — biar audit trail tetap
        // terjaga. Aplikasi harus handle ini (soft-delete atau status pembatalan).
        $this->db->query('ALTER TABLE transaksi ADD CONSTRAINT transaksi_pesanan_acara_id_fk FOREIGN KEY (pesanan_acara_id) REFERENCES pesanan_acara(id) ON DELETE RESTRICT ON UPDATE CASCADE');

        // CHECK constraint: salah satu NOT NULL, TIDAK boleh keduanya
        // MySQL 8.0.16+ support CHECK. MariaDB 10.2+ support CHECK.
        $this->db->query('ALTER TABLE transaksi ADD CONSTRAINT transaksi_xor_check CHECK ((pesanan_id IS NOT NULL AND pesanan_acara_id IS NULL) OR (pesanan_id IS NULL AND pesanan_acara_id IS NOT NULL))');
    }

    public function down()
    {
        // Rollback urutan terbalik

        // 4. Drop CHECK + FK + kolom pesanan_acara_id, kembalikan pesanan_id NOT NULL
        $this->db->query('ALTER TABLE transaksi DROP CONSTRAINT transaksi_xor_check');
        $this->db->query('ALTER TABLE transaksi DROP FOREIGN KEY transaksi_pesanan_acara_id_fk');
        $this->db->query('DROP INDEX transaksi_pesanan_acara_id_idx ON transaksi');
        $this->forge->dropColumn('transaksi', 'pesanan_acara_id');
        $this->db->query('ALTER TABLE transaksi MODIFY COLUMN pesanan_id INT(10) UNSIGNED NOT NULL');

        // 3. SKIP — pesanan_acara schema di-handle oleh 000006a (down() di sana)

        // 2. Drop biaya_stand dari pengaturan
        $this->forge->dropColumn('pengaturan', 'biaya_stand');

        // 1. Drop kolom visibility & kg dari produk
        $this->forge->dropColumn('produk', [
            'satuan', 'step_qty', 'min_qty', 'tampil_di_pesan_antar', 'tampil_di_pesan_stand',
        ]);
    }
}
