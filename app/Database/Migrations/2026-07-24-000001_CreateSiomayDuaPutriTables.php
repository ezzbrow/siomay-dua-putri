<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSiomayDuaPutriTables extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'nama_toko' => ['type' => 'VARCHAR', 'constraint' => 255],
            'username' => ['type' => 'VARCHAR', 'constraint' => 100],
            'password_hash' => ['type' => 'VARCHAR', 'constraint' => 255],
            'nomor_hp' => ['type' => 'VARCHAR', 'constraint' => 30],
            'email' => ['type' => 'VARCHAR', 'constraint' => 255],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('username');
        $this->forge->addUniqueKey('email');
        $this->forge->createTable('admin', true);

        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'nama' => ['type' => 'VARCHAR', 'constraint' => 255],
            'kategori' => ['type' => 'VARCHAR', 'constraint' => 50],
            'harga' => ['type' => 'DECIMAL', 'constraint' => '12,2'],
            'status_aktif' => ['type' => 'BOOLEAN', 'default' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('produk', true);

        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'produk_id' => ['type' => 'INT', 'unsigned' => true],
            'nama_varian' => ['type' => 'VARCHAR', 'constraint' => 100],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('produk_id', 'produk', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('varian_produk', true);

        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'minimum_order' => ['type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 100000.00],
            'pajak_persen' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => 10.00],
            'pajak_aktif' => ['type' => 'BOOLEAN', 'default' => false],
            'alamat_umkm' => ['type' => 'VARCHAR', 'constraint' => 500],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('pengaturan', true);

        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'kode_pesanan' => ['type' => 'VARCHAR', 'constraint' => 50],
            'nama_pembeli' => ['type' => 'VARCHAR', 'constraint' => 255],
            'nomor_hp' => ['type' => 'VARCHAR', 'constraint' => 30],
            'metode' => ['type' => 'VARCHAR', 'constraint' => 30],
            'alamat' => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            'catatan' => ['type' => 'TEXT', 'null' => true],
            'subtotal' => ['type' => 'DECIMAL', 'constraint' => '12,2'],
            'pajak' => ['type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 0],
            'total' => ['type' => 'DECIMAL', 'constraint' => '12,2'],
            'status' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'pending'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('kode_pesanan');
        $this->forge->createTable('pesanan', true);

        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'pesanan_id' => ['type' => 'INT', 'unsigned' => true],
            'produk_id' => ['type' => 'INT', 'unsigned' => true],
            'varian_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'jumlah' => ['type' => 'INT', 'unsigned' => true],
            'harga_satuan' => ['type' => 'DECIMAL', 'constraint' => '12,2'],
            'subtotal_item' => ['type' => 'DECIMAL', 'constraint' => '12,2'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('pesanan_id', 'pesanan', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('produk_id', 'produk', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('varian_id', 'varian_produk', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('item_pesanan', true);

        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'pesanan_id' => ['type' => 'INT', 'unsigned' => true],
            'midtrans_order_id' => ['type' => 'VARCHAR', 'constraint' => 100],
            'status_pembayaran' => ['type' => 'VARCHAR', 'constraint' => 30],
            'mdr_persen' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => 0],
            'nominal_diterima' => ['type' => 'DECIMAL', 'constraint' => '12,2'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('pesanan_id');
        $this->forge->addUniqueKey('midtrans_order_id');
        $this->forge->addForeignKey('pesanan_id', 'pesanan', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('transaksi', true);
    }

    public function down()
    {
        $this->forge->dropTable('transaksi', true);
        $this->forge->dropTable('item_pesanan', true);
        $this->forge->dropTable('pesanan', true);
        $this->forge->dropTable('pengaturan', true);
        $this->forge->dropTable('varian_produk', true);
        $this->forge->dropTable('produk', true);
        $this->forge->dropTable('admin', true);
    }
}
