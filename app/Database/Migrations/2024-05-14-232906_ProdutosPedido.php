<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class ProdutosPedido extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true
            ],
            'produtoId' => [
                'type' => 'INT',
                'unsigned' => true
            ],
            'pedidoId' => [
                'type' => 'INT',
                'unsigned' => true
            ],
            'quantidade' => [
                'type' => 'INT'
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
        $this->forge->addForeignKey('produtoId', 'produtos', 'id');
        $this->forge->createTable('produtos_pedido');
    }

    public function down()
    {
        $this->forge->dropTable('produtos_pedido');
    }
}
