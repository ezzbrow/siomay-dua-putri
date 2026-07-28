<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPembeliIdToPesanan extends Migration
{
    public function up()
    {
        $this->forge->addColumn('pesanan', [
            'pembeli_id' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'metode',
            ],
        ]);
        $this->db->query('CREATE INDEX pesanan_pembeli_id_idx ON pesanan (pembeli_id)');
        $this->db->query('ALTER TABLE pesanan ADD CONSTRAINT pesanan_pembeli_id_fk FOREIGN KEY (pembeli_id) REFERENCES pembeli(id) ON DELETE SET NULL ON UPDATE CASCADE');
    }

    public function down()
    {
        $this->db->query('ALTER TABLE pesanan DROP FOREIGN KEY pesanan_pembeli_id_fk');
        $this->db->query('DROP INDEX pesanan_pembeli_id_idx ON pesanan');
        $this->forge->dropColumn('pesanan', 'pembeli_id');
    }
}
