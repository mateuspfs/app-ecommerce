<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class All extends Seeder
{
    public function run()
    {
        $this->call('User');
        $this->call('Admin');
        $this->call('Categoria');
        $this->call('Produto');
        $this->call('Cupom');
        $this->call('Carrinho');
        $this->call('Pedido');
    }
}
