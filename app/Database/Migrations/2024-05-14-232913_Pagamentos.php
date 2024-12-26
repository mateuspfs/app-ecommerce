<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class Pagamentos extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true
            ],
            'pedidoId' => [
                'type' => 'INT',
                'unsigned' => true
            ],
            'method' => [
                'type' => 'VARCHAR',
                'constraint' => 100
            ],
            'response_pagseguro' => [
                'type' => 'MEDIUMTEXT',
                'null' => true
            ],
            'status' => [
                'type' => 'TEXT',
            ],
            'paid_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
                'default' => null,
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
            'updated_at' => [
                'type' => 'TIMESTAMP',
                'default' => new RawSql('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
            ]
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('pedidoId', 'pedidos', 'id');
        $this->forge->createTable('pagamentos');
    }

    public function down()
    {
        $this->forge->dropTable('pagamentos');
    }
}
