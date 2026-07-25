<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddJamOperasionalToPengaturan extends Migration
{
    public function up()
    {
        $this->forge->addColumn('pengaturan', [
            'jam_buka' => [
                'type' => 'TIME',
                'null' => true,
                'after' => 'alamat_umkm',
            ],
            'jam_tutup' => [
                'type' => 'TIME',
                'null' => true,
                'after' => 'jam_buka',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('pengaturan', ['jam_buka', 'jam_tutup']);
    }
}
