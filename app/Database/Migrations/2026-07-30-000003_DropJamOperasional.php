<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;

class DropJamOperasional extends Migration
{
    public function up()
    {
        $this->forge->dropColumn('pengaturan', ['jam_buka', 'jam_tutup']);
    }

    public function down()
    {
        $this->forge->addColumn('pengaturan', [
            'jam_buka'  => ['type' => 'TIME', 'null' => true],
            'jam_tutup' => ['type' => 'TIME', 'null' => true],
        ]);
    }
}