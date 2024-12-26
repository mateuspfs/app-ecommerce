<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class Carrinho extends Migration
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
            'userId' => [
                'type' => 'INT',
                'unsigned' => true
            ],
            'quantidade' => [
                'type' => 'INT',
                'unsigned' => true
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
        $this->forge->addForeignKey('produtoId', 'produtos', 'id');
        $this->forge->addForeignKey('userId', 'users', 'id');
        $this->forge->createTable('carrinho');
    }

    public function down()
    {
        $this->forge->dropTable('carrinho');
    }
}
