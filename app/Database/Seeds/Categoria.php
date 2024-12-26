<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class Categoria extends Seeder
{
    public function run()
    {
        $nomes = [
            'Casa',
            'Eletrônicos',
            'Informática',
            'Moda Masculina',
            'Moda Feminina',
            'Moda Infantil',
            'Calçados',
            'Acessórios',
            'Beleza e Cuidados Pessoais',
            'Saúde',
            'Alimentos e Bebidas',
            'Esportes e Lazer',
            'Ferramentas e Jardim',
            'Automotivo',
            'Livros',
            'Móveis',
            'Decoração',
            'Brinquedos',
            'Papelaria e Escritório',
            'Pet Shop',
            'Eletrodomésticos',
            'Telefonia',
            'Fotografia e Filmagem',
            'Música e Instrumentos Musicais',
            'Jogos e Consoles',
            'Relógios',
            'Óculos e Lentes',
            'Viagem e Turismo',
            'Bebês',
            'Cama, Mesa e Banho'
        ];  

        for($i=1;$i<=29;$i++){
            $data[] = [
                'nome' => $nomes[$i],
            ];
        }
        
        $this->db->table('categorias')->insertBatch($data);
    }
}
