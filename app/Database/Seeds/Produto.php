<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class Produto extends Seeder
{
    public function run()
    {
        $faker = \Faker\Factory::create('pt_BR');

        for ($i = 0; $i < 100; $i++) {
            $preco_c = ($i%2 === 0) ? $faker->randomFloat(2, 10, 1000) : null; 
            $preco = ($preco_c === null) ? $faker->randomFloat(2, 10, 1000) : $faker->numberBetween(0, $preco_c) ;

            $data = [
                'categoriaId' => $faker->numberBetween(1, 29),
                'nome' => $faker->sentence(3),
                'slug' => $faker->slug,
                'descricao' => $faker->paragraph(3),
                'preco' => $preco,
                'preco_comparativo' => $preco_c,
                'estoque' => $faker->numberBetween(10, 100),
                'img' => '/assets/product_images/product-example.jpg', 
            ];

            $this->db->table('produtos')->insert($data);
        }
    }
}
