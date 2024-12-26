<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class Cupons extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',  
                'unsigned' => true,
                'auto_increment' => true
            ],
            'nome' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'codigo' => [
                'type' => 'VARCHAR',
                'constraint' => 100
            ],
            'tipo' => [
                'type' => 'ENUM',
                'constraint' => ['f', 'p'],
            ],
            'desconto' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2'
            ],
            'qt_disponivel' => [
                'type' => 'INT'
            ],
            'qt_cliente' => [
                'type' => 'INT'
            ],
            'qt_usada' => [
                'type' => 'INT',
                'null' => true,
                'default' => null
            ],
            'status' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
            ],
            'expired_at' => [
                'type' => 'TIMESTAMP',
            ],
            'deleted_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
                'defult' => null
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
        $this->forge->addUniqueKey('codigo');
        $this->forge->createTable('cupons');
    }

    public function down()
    {
        $this->forge->dropTable('cupons');
    }
}
