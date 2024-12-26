<?php

namespace App\Models;

use CodeIgniter\Model;

class PaymentModel extends Model
{
    protected $table            = 'pagamentos';  
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['pedidoId', 'method', 'response_pagseguro', 'status', 'paid_at', 'created_at', 'updated_at'];

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
    protected $validationRules = [
        'pedidoId' => 'required|integer|is_not_unique[pedidos.id]',
        'method' => 'string|in_list[pix,credit-card,boleto]',
        'response_pagseguro' => 'permit_empty|string',
        'status' => 'required|string',
        'paid_at' => 'permit_empty|valid_date'
    ];

    protected $validationMessages = [
        'pedidoId' => [
            'required' => 'O campo Pedido ID é obrigatório.',
            'integer' => 'O campo Pedido ID deve ser um número inteiro.',
            'is_not_unique' => 'O Pedido ID especificado não existe.'
        ],
        'status' => [
            'required' => 'O campo status é obrigatório.',
            'string' => 'O campo metódo deve ser uma string.'
        ],
        'response_pagseguro' => [
            'string' => 'O campo Resposta de Pagamento deve ser uma string.'
        ],
        'method' => [
            'string' => 'O campo metódo deve ser uma string.',
            'in_list' => 'Método informado difere dos aceitos.'
        ],
    ];

    protected $skipValidation = false;
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
