<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTanggalDibutuhkanToPesanan extends Migration
{
    public function up()
    {
        // Menambahkan kolom `tanggal_dibutuhkan` (DATE, NOT NULL) ke tabel `pesanan`,
        // mengikuti revisi §9 CLAUDE.md (Jalur A dihapus, semua pesanan sekarang
        // pre-order dengan tanggal kebutuhan minimal H+1).
        //
        // PRASYARAT SEBELUM MENJALANKAN: tabel `pesanan` harus kosong (tidak ada row
        // lama tanpa tanggal_dibutuhkan). Kalau tidak kosong, ALTER ADD NOT NULL
        // akan gagal. Migration ini TIDAK mem-backfill data historis.
        $this->forge->addColumn('pesanan', [
            'tanggal_dibutuhkan' => [
                'type'       => 'DATE',
                'null'       => false,
                'after'      => 'catatan',
            ],
        ]);
        $this->db->query('CREATE INDEX pesanan_tanggal_dibutuhkan_idx ON pesanan (tanggal_dibutuhkan)');
    }

    public function down()
    {
        $this->db->query('DROP INDEX pesanan_tanggal_dibutuhkan_idx ON pesanan');
        $this->forge->dropColumn('pesanan', 'tanggal_dibutuhkan');
    }
}
