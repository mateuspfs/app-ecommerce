<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table            = 'produtos';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = ['id', 'categoriaId', 'nome', 'slug', 'descricao', 'preco', 'estoque', 'preco_comparativo', 'img', 'delete_at', 'status'];

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
        'nome' => 'required|max_length[100]',
        'slug' => 'required|max_length[200]|is_unique[produtos.slug,produtos.id]',
        'descricao' => 'required|max_length[800]',
        'preco' => 'required|numeric',
        'estoque' => 'required|integer',
        'preco_comparativo' => 'permit_empty|numeric',
        'img' => 'required|max_length[255]',
    ];
    protected $validationMessages = [
        'nome' => [
            'required' => 'O nome é obrigatório.',
            'max_length' => 'O nome não pode ter mais de 100 caracteres.'
        ],
        'slug' => [
            'required' => 'O slug é obrigatório.',
            'max_length' => 'O slug não pode ter mais de 200 caracteres.',
            'is_unique' => 'O slug já existe no sistema.'
        ],
        'descricao' => [
            'required' => 'A descrição é obrigatória.',
            'max_length' => 'A descrição não pode ter mais de 800 caracteres.'
        ],
        'preco' => [
            'required' => 'O preço é obrigatório.',
            'numeric' => 'O preço deve ser um valor numérico.'
        ],
        'estoque' => [
            'required' => 'O estoque é obrigatório.',
            'integer' => 'O estoque deve ser um valor inteiro.'
        ],
        'preco_comparativo' => [
            'numeric' => 'O preço de comparação deve ser um valor númerico válido.'
        ],
        'img' => [
            'required' => 'A imagem é obrigatória.',
            'max_length' => 'O caminho da imagem não pode ter mais de 255 caracteres.'
        ],
        'status' => [
            'required' => 'O status é obrigatório.',
            'in_list' => 'Ocorreu um erro'
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
