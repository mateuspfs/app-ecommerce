<?php

namespace App\Database\Migrations;

use App\Database\Seeds\User;
use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class Users extends Migration
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
                'constraint' => 100               
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 220               
            ],
            'telefone' => [
                'type' => 'VARCHAR',
                'constraint' => 11               
            ],
            'cpf' => [
                'type' => 'CHAR',
                'constraint' => 11              
            ],
            'credito' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2'      
            ],
            'password' => [
                'type' => 'VARCHAR',
                'constraint' => 100              
            ],
            'status' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
            ],
            'deleted_at' => [
                'type' => 'TIMESTAMP',
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
        $this->forge->addUniqueKey('email');    
        $this->forge->addUniqueKey('cpf');
        $this->forge->createTable('users');
    }

    public function down()
    {
        $this->forge->dropTable('users');
    }
}
