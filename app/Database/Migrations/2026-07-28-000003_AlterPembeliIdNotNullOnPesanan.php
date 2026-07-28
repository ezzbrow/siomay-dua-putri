<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterPembeliIdNotNullOnPesanan extends Migration
{
    public function up()
    {
        // Migrasi ini mengubah kolom `pembeli_id` di tabel `pesanan` dari nullable
        // menjadi NOT NULL, mengikuti revisi §9 CLAUDE.md (Jalur A dihapus, semua
        // pesanan sekarang diasosiasikan ke akun pembeli).
        //
        // PRASYARAT SEBELUM MENJALANKAN: pastikan tidak ada row dengan pembeli_id NULL.
        // Migration ini TIDAK mem-backfill data. Kalau ada row NULL, ALTER akan gagal
        // di MySQL dan langkah ini harus dihentikan manual.
        //
        // NB: foreign key pesanan_pembeli_id_fk didefinisikan dengan ON DELETE SET NULL
        // (lihat migration 2026-07-28-000002). Karena kolom tujuan sekarang NOT NULL,
        // SET NULL tidak lagi valid. Kita drop FK, ubah kolom, lalu re-add FK dengan
        // ON DELETE RESTRICT (pembeli tidak boleh dihapus kalau masih punya pesanan).

        $this->db->query('ALTER TABLE pesanan DROP FOREIGN KEY pesanan_pembeli_id_fk');
        $this->db->query('ALTER TABLE pesanan MODIFY COLUMN pembeli_id INT(10) UNSIGNED NOT NULL');
        $this->db->query('ALTER TABLE pesanan ADD CONSTRAINT pesanan_pembeli_id_fk FOREIGN KEY (pembeli_id) REFERENCES pembeli(id) ON DELETE RESTRICT ON UPDATE CASCADE');
    }

    public function down()
    {
        // Kembalikan ke nullable + SET NULL FK
        $this->db->query('ALTER TABLE pesanan DROP FOREIGN KEY pesanan_pembeli_id_fk');
        $this->db->query('ALTER TABLE pesanan MODIFY COLUMN pembeli_id INT(10) UNSIGNED NULL');
        $this->db->query('ALTER TABLE pesanan ADD CONSTRAINT pesanan_pembeli_id_fk FOREIGN KEY (pembeli_id) REFERENCES pembeli(id) ON DELETE SET NULL ON UPDATE CASCADE');
    }
}
