<?php

namespace App\Models;

use CodeIgniter\Model;

class CupomModel extends Model
{
    protected $table            = 'cupons';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = ['nome', 'codigo', 'tipo', 'desconto','qt_disponivel', 'qt_cliente', 'qt_usada', 'status', 'expired_at'];

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
        'nome' => 'required|max_length[100]',
        'tipo' => 'required|in_list[f,p]',
        'desconto' => 'required|decimal|greater_than[0]',
        'qt_disponivel' => 'required|integer|greater_than[0]',
        'qt_cliente' => 'required|integer|greater_than[0]',
        'expired_at' => 'required|valid_date',
    ];

    protected $validationMessages   = [
        'nome' => [
            'required' => 'O nome é obrigatório.',
            'max_length' => 'O nome não pode ter mais de 100 caracteres.'
        ],
        'tipo' => [
            'required' => 'O tipo é obrigatório.',
            'in_list' => 'O tipo deve ser "f" para fixo ou "p" para percentual.'
        ],
        'desconto' => [
            'required' => 'O desconto é obrigatório.',
            'decimal' => 'O desconto deve ser um valor decimal.',
            'greater_than' => 'O desconto deve ser maior que zero.'
        ],
        'qt_disponivel' => [
            'required' => 'A quantidade é obrigatória.',
            'integer' => 'A quantidade deve ser um valor inteiro.',
            'greater_than' => 'A quantidade deve ser maior que zero.'
        ],
        'qt_cliente' => [
            'required' => 'A quantidade por cliente é obrigatória.',
            'integer' => 'A quantidade por cliente deve ser um valor inteiro.',
            'greater_than' => 'A quantidade por cliente deve ser maior que zero.'
        ],
        'expired_at' => [
            'required' => 'A data de expiração é obrigatória.',
            'valid_date' => 'A data de expiração deve ser uma data válida.'
        ],
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
