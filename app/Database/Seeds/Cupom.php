<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class Cupom extends Seeder
{
    public function run()
    {
        for ($i = 1; $i <= 50; $i++) {
            $data[] = [
                'nome' => 'Desconto Padrão ' . $i,
                'codigo' => 'CODIGO' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'tipo' => (rand(0, 1) === 1) ? 'p' : 'f', 
                'desconto' => rand(5, 50),  
                'qt_disponivel' => rand(10, 100), 
                'qt_cliente' => rand(1, 5), 
                'status' => rand(0, 1),  
                'expired_at' => date('Y-m-d H:i:s', strtotime('+'.rand(1, 365).' days')),  // Data de expiração aleatória até 1 ano no futuro
            ];
        }

        $this->db->table('cupons')->insertBatch($data);
    }
}
