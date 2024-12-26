<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use Faker\Factory;

class Carrinho extends Seeder
{
    public function run()
    {
        $faker = Factory::create('pt_BR');

        for($i=1;$i<=30;$i++){
            for($x=1;$x<=10;$x++){
                $data[] = [
                    'userId' => $i,
                    'produtoId' => $x, 
                    'quantidade' => random_int(1,10),
                ];
            }
        }

        $this->db->table('carrinho')->insertBatch($data);
    }
}
