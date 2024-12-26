<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CategoryModel;
use CodeIgniter\HTTP\ResponseInterface;

class CategoryController extends BaseController
{
    protected $categoryModel;
    protected $request;

    public function __construct()
    {
        $this->categoryModel = new CategoryModel();
        $this->request = \Config\Services::request();
    }

    public function index()
    {
        $pagination = service('pager');
        
        $query = $this->filter(); 

        if($query->countAllResults(false) < 1){
            return redirect()->route('category.index')->with('error', 'Sem resultados para sua busca');
        }

        $data = [
            'categories' => $query->paginate(10),
            'pagination' => $pagination
        ];
        
        return view('admin/categories', $data);
    }

    public function store()
    {
        $validationRules = [
            'nome' => 'required|is_unique[categorias.nome]',
        ];
    
        $validationMessages = [
            'nome' => [
                'required' => 'O nome é obrigatório',
                'is_unique' => 'Nome de categoria já cadastrado no sistema, deve ser único',
            ]
        ];
    
        if (!$this->validate($validationRules, $validationMessages)) {
            return redirect()->route('category.index')->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = $this->request->getPost();
        $inserted = $this->categoryModel->insert($data);

        if(!$inserted){
            return redirect()->route('category.index')->withInput()->with('errors', $this->categoryModel->errors());
        }

        return redirect()->route('category.index')->with('success', 'Cadastrado com sucesso!');
    }

    public function update($id)
    {
        $validationRules = [
            'nome' => 'required|is_unique[categorias.nome,categorias.id,'.$id.']',
        ];
    
        $validationMessages = [
            'nome' => [
                'required' => 'O nome é obrigatório',
                'is_unique' => 'Nome de categoria já cadastrado no sistema, deve ser único',
            ]
        ];
    
        if (!$this->validate($validationRules, $validationMessages)) {
            return redirect()->route('category.index')->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = $this->request->getPost();
        $category = $this->categoryModel->find($id);

        if(!$category){
            return redirect()->back()->with('error', 'Ocorreu um erro ao atualizar categoria.');
        }
      
        $updated = $this->categoryModel->update($id, [
            'nome' => $data['nome'],
            'status' => $data['status']
        ]);
        
        if(!$updated){
            return redirect()->route('category.index')->withInput()->with('errors', $this->categoryModel->errors());
        }

        return redirect()->route('category.index')->with('success', 'Categoria atualizada com sucesso');
    }

    public function destroy($id)
    {
        $categories = $this->categoryModel->find($id);

        if(!$categories){
            return redirect()->back()->with('error', 'Ocorreu um erro.');
        }

        $this->categoryModel->delete($id);

        return redirect()->route('category.index')->with('success', 'Excluída com sucesso.');
    }

    public function xlsx()
    {
        $apiUrl = 'https://jsonforexcel.azurewebsites.net/api/Central/Json/Excel';

        $query = $this->filter(); 

        $jsonData = $query->findAll();

        $dataCategories = array_map(function ($dataCategory) {
            $status = $dataCategory->status === '1' ? 'Ativo' : 'Inativo';
            return [
                'Nome' => $dataCategory->nome,
                'Status' => $status,
                'Criado em' => date('d/m/Y', strtotime($dataCategory->created_at)),
                'Última atualização' => date('d/m/Y', strtotime($dataCategory->updated_at)),
                'Excluído em' => $dataCategory->deleted_at === null ? 'N\Excluido' : date('d/m/Y', strtotime($dataCategory->deleted_at))
            ];
        }, $jsonData);

        $categories = json_encode($dataCategories);

        $requestData = [
            'json' => $categories,
            'sheetName' => 'CategoriesList',
            'type' => 'base64',
        ];

        $client = service('curlrequest');
        $response = $client->post($apiUrl, [
            'headers' => [
                'Content-Type' => 'application/json', 
            ],
            'body' => json_encode($requestData),
            'http_errors' => false,
            'verify' => false, 
        ]);

        $statusCode = $response->getStatusCode();
        if ($statusCode === 200) {
            $fileContentsBase64 = $response->getBody();
            $fileContents = base64_decode($fileContentsBase64);

            return $this->response
                        ->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
                        ->setHeader('Content-Disposition', 'attachment; filename="list_category.xlsx"')
                        ->setBody($fileContents);
        } else {
            return redirect()->back()->with('error', 'Erro ao extrair planilha');
        }
    }

    public function getCategories()
    {
        $categories = $this->categoryModel
                        ->orderBy('nome', 'asc')
                        ->where('status', '1')
                        ->findAll();
                        
        return $this->response->setJSON($categories);
    }

    private function filter()
    {
        $search = $this->request->getVar('search');
        $status = $this->request->getVar('status');

        $query = $this->categoryModel->orderBy('status', 'DESC')->orderBy('id', 'DESC');
        
        if ($status !== null && $status !== '') {
            $query->where('status', $status); 
        }

        if ($search) {
            $query->like('nome', $search);
        }

        return $query;
    }
}
