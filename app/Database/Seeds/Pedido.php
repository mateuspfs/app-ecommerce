<?php

namespace App\Database\Seeds;

use App\Models\OrderModel;
use CodeIgniter\Database\Seeder;
use DateTime;
use Faker\Core\DateTime as CoreDateTime;
use Faker\Factory;

class Pedido extends Seeder
{
    public function run()
    {
        $faker = Factory::create('pt_BR');

        for ($i = 1; $i <= 30; $i++) {
            $cupomId = random_int(0, 49);
            $cupomId = ($cupomId === 0) ? null : $cupomId;
            $timestamp = new DateTime();
            $currentTimestamp = $timestamp->getTimestamp();

            $dataPedido = [
                'codigo' => $this->gerarCodigo(),
                'userId' => $i,
                'cupomId' => $cupomId,
                'endereco' => '{"cep":"11720-000","rua":"Rua Alcino Vicente Leal","bairro":"Antártica","numero":"1678867968687","cidade":"Praia Grande","uf":"SP","complemento":"nenhum"}',
                'valor' => $faker->randomFloat(2, 1000, 10000),
                'status' => 'Pago',
            ];

            $this->db->table('pedidos')->insert($dataPedido);

            for($x = 1; $x <=10; $x++){
                $dataProdutosPedido = [
                    'pedidoId' => $i,
                    'produtoId' => $x,
                    'quantidade' => random_int(0, 50),
                ];
                $this->db->table('produtos_pedido')->insert($dataProdutosPedido);
            }
            
            $dataPagamentoPedido = [
                'pedidoId' => $i,
                'method' => 'pix',
                'paid_at' => $currentTimestamp,
                'status' => 'Pago',
            ];
            
            $this->db->table('pagamentos')->insert($dataPagamentoPedido);
        }
    }

    private function gerarCodigo()
    {
        $caracteres = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $codigo = '';
        $orderModel = new OrderModel();

        do {
            $codigo = '';
            for ($i = 0; $i < 8; $i++) {
                $codigo .= $caracteres[rand(0, strlen($caracteres) - 1)];
            }
            $codigoExists = $orderModel->where('codigo', $codigo)->first();
        } while ($codigoExists);

        return $codigo;
    }
}
