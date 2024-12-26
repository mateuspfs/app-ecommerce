<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class ImagensProduto extends Migration
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
                'unsigned' => true,
            ],
            'caminho' => [
                'type' => 'VARCHAR',
                'constraint' => 100
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'default' => new RawSql('CURRENT_TIMESTAMP')
            ],
            'updated_at' => [
                'type' => 'TIMESTAMP',
                'default' => new RawSql('CURRENT_TIMESTAMP')
            ]
        ]);
        
        $this->forge->addForeignKey('produtoId', 'produtos', 'id');
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('imagens_produto');
    }

    public function down()
    {
        $this->forge->dropTable('imagens_produto');
    }
}
