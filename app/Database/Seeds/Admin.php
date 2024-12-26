<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use Faker\Factory;

class Admin extends Seeder
{
    public function run()
    {
        $faker = Factory::create('pt_BR');

        $data = [];

        $data[] = [
            'nome' => 'Mateus Sampaio',
            'email' => '4444@gmail.com', 
            'password' => password_hash('12345', PASSWORD_DEFAULT),
        ];

        $data[] = [
            'nome' => 'ADMIN',
            'email' => 'admin@admin.com', 
            'password' => password_hash('123', PASSWORD_DEFAULT),
        ];

        for($i=1;$i<=100;$i++){
            $data[] = [
                'nome' => $faker->firstName . ' ' . $faker->lastName,
                'email' => $faker->email, 
                'password' => password_hash('12345', PASSWORD_DEFAULT),
            ];
        }

        $this->db->table('admins')->insertBatch($data);
    }
}
