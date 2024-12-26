<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = ['nome', 'cpf','email', 'telefone', 'password', 'credito', 'status'];

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
        'nome' => 'required',
        'telefone' => 'required|min_length[10]|max_length[11]',
        'password' => 'required',
        'credito' => 'permit_empty|numeric',
    ];
    protected $validationMessages   = [
        'nome' => [
            'required' => 'O nome é obrigatório'
        ],
        'telefone' => [
            'required' => 'O telefone é obrigatório',
            'max_length' => 'O telefone tem um tamanho inválido, maior',
            'min_length' => 'O telefone tem um tamanho inválido, menor',
        ],
        'password' => [
            'required' => 'A senha é obrigatória'
        ],
        'credito' => [
            'numeric' => 'Ocorreu um erro.'
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
