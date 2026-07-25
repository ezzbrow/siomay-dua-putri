<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRememberTokenToAdmin extends Migration
{
    public function up()
    {
        $this->forge->addColumn('admin', [
            'remember_token' => [
                'type'       => 'VARCHAR',
                'constraint' => 64,
                'null'       => true,
                'after'      => 'password_hash',
            ],
        ]);
        $this->db->query('CREATE INDEX admin_remember_token_idx ON admin (remember_token)');
    }

    public function down()
    {
        $this->db->query('DROP INDEX admin_remember_token_idx ON admin');
        $this->forge->dropColumn('admin', 'remember_token');
    }
}
