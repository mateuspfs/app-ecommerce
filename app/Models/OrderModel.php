<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderModel extends Model
{
    protected $table            = 'pedidos';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = ['codigo','userId','cupomId','cep','rua','endereco','valor','status'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'codigo' => 'required|exact_length[8]|alpha_numeric',
        'userId' => 'required|integer',
        'cupomId' => 'permit_empty|integer',
        'valor' => 'required|numeric',
        'status' => 'string',
    ];

    protected $validationMessages   = [
        'codigo' => [
            'required' => 'O campo Código é obrigatório.',
            'exact_length' => 'O campo Código deve ter exatamente 8 caracteres.',
            'alpha_numeric' => 'O campo Código deve conter apenas caracteres alfanuméricos.'
        ],
        'userId' => [
            'required' => 'O campo User ID é obrigatório.',
            'integer' => 'O campo User ID deve ser um número inteiro.'
        ],
        'cupomId' => [
            'integer' => 'O campo Cupom ID deve ser um número inteiro.'
        ],
        'valor' => [
            'required' => 'O campo Valor é obrigatório.',
            'numeric' => 'O campo Valor deve ser um número.'
        ],
        'status' => [
            'string' => 'Ocorreu um erro',
        ]
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];
}
