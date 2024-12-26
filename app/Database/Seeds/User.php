<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use Faker\Factory;

class User extends Seeder
{
    public function run()
    {
        $faker = Factory::create('pt_BR');

        $data[] = [
            'nome' => 'Mateus',
            'email' => 'mateupfs123@gmail.com',
            'cpf' => '39491150804',
            'telefone' => '13981739957',
            'password' => password_hash('12345', PASSWORD_DEFAULT),
        ];

        for($i=1;$i<=100;$i++){
            $cpf = $this->generateFakeCpf();

            $data[] = [
                'nome' => $faker->firstName . ' ' . $faker->lastName,
                'email' => $faker->email, 
                'telefone' => preg_replace('/[^0-9]/is', '', $faker->phoneNumber()),
                'cpf' => $cpf, 
                'password' => password_hash('12345', PASSWORD_DEFAULT),
            ];
        }

        $this->db->table('users')->insertBatch($data);
    }

    function generateFakeCpf(): string
    {
        $cpf = '';
        for ($i = 0; $i < 9; $i++) {
            $cpf .= rand(0, 9);
        }

        $cpf .= $this->calculateCpfDigit($cpf);

        $cpf .= $this->calculateCpfDigit($cpf);

        return $cpf;
    }

    function calculateCpfDigit(string $base): int
    {
        $length = strlen($base);
        $sum = 0;
        for ($i = 0; $i < $length; $i++) {
            $sum += $base[$i] * (($length + 1) - $i);
        }
        $remainder = $sum % 11;
        return ($remainder < 2) ? 0 : 11 - $remainder;
    }
}
