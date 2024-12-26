<?php

namespace App\Models;

use CodeIgniter\Model;

class CartModel extends Model
{
    protected $table            = 'carrinho';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['produtoId', 'userId', 'quantidade'];

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
        'produtoId' => 'required',
        'userId' => 'required',
        'quantidade' => 'required|numeric|is_natural_no_zero'
    ];
    protected $validationMessages   = [
        'produtoId' => [
            'required' => 'Produto obrigatório',
        ],
        'userId' => [
            'required' => 'Id de usuário obrigatório',
        ],
        'quantidade' => [
            'required' => 'Quantidade obrigatória',
            'numeric' => 'Quantidade deve ser um número inteiro',
            'is_natural_no_zero' => 'Quantidade não pode ser zero!'
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
