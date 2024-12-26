<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class Pedidos extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true
            ],
            'codigo' => [
                'type' => 'CHAR',
                'constraint' => '8'
            ],
            'userId' => [
                'type' => 'INT',
                'unsigned' => true
            ],
            'cupomId' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => true
            ],
            'endereco' => [
                'type' => 'MEDIUMTEXT'
            ],
            'valor' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2'
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 30,
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
            'updated_at' => [
                'type' => 'TIMESTAMP',
                'default' => new RawSql('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
            ],
            'deleted_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ]
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('codigo');
        $this->forge->addForeignKey('userId', 'users', 'id');
        $this->forge->addForeignKey('cupomId', 'cupons', 'id');
        $this->forge->createTable('pedidos');
    }

    public function down()
    {
        $this->forge->dropTable('pedidos');
    }
}
