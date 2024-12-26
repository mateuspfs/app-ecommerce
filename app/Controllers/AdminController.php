<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AdminModel;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Pager\PagerRenderer;

class AdminController extends BaseController
{
    protected $adminModel;
    protected $request;

    public function __construct()
    {
        $this->adminModel = new AdminModel();
        $this->request = \Config\Services::request();
    }

    public function index()
    {
        $pagination = service('pager');
    
        $query = $this->filter(); 

        if($query->countAllResults(false) < 1){
            return redirect()->route('admin.index')->with('error', 'Sem resultados para sua busca');
        }

        $data = [
            'admins' => $query->paginate(10),
            'pagination' => $pagination
        ];
        
        return view('admin/admins', $data);
    }

    public function store()
    {
        $validationRules = [
            'email' => 'required|valid_email|is_unique[admins.email]',
        ];
    
        $validationMessages = [
            'email' => [
                'required' => 'O email é obrigatório',
                'valid_email' => 'O email deve ser válido',
                'is_unique' => 'Email já cadastrado no sistema',
            ]
        ];
    
        if (!$this->validate($validationRules, $validationMessages)) {
            return redirect()->route('admin.index')->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = $this->request->getPost();

        if(!($data['password'] === $data['cpassword'])){
            return redirect()->back()->withInput()->with('error', 'Senhas não coincidem.');
        }

        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        $inserted = $this->adminModel->insert($data);

        if(!$inserted){
            return redirect()->back()->withInput()->with('errors', $this->adminModel->errors());
        }

        return redirect()->back()->with('success', 'Cadastrado com sucesso!');
    }

    public function update($id)
    {
        $validationRules = [
            'email' => 'required|valid_email|is_unique[admins.email,admins.id,' . $id. ']',
        ];
    
        $validationMessages = [
            'email' => [
                'required' => 'O email é obrigatório',
                'valid_email' => 'O email deve ser válido',
                'is_unique' => 'Email já cadastrado no sistema',
            ]
        ];
    
        if (!$this->validate($validationRules, $validationMessages)) {
            return redirect()->route('admin.index')->withInput()->with('errors', $this->validator->getErrors());
        }

        $verifyAdmin = $this->verifyAdmin($id);

        if($verifyAdmin){
            return redirect()->route('admin.index')->with('error', 'Você não pode ter ações sobre si mesmo.');
        }

        $data = $this->request->getPost();
        
        if($data['password'] xor $data['cpassword']){
            return redirect()->back()->with('error', 'Prencha a senha e confimação de senha para atualizar corretamente.');
        }

        $admin = $this->adminModel->find($id);

        if(!$admin){
            return redirect()->back()->with('error', 'Ocorreu um erro ao atualizar usuário.');
        }

        if(!($data['password'] && $data['cpassword'])){
            $updated = $this->adminModel->update($id, [
                'nome' => $data['nome'],
                'email' => $data['email'],
                'status' => $data['status']
            ]);
            
            if(!$updated){
                return redirect()->back()->withInput()->with('errors', $this->adminModel->errors());
            }

            return redirect()->back()->with('success', 'Usuário atualizado com sucesso');
        } 

        if(!($data['password'] === $data['cpassword'])){
            return redirect()->back()->with('error', 'Senhas não coincidem.');
        } 

        $updated = $this->adminModel->update($id, [
            'nome' => $data['nome'],
            'email' => $data['email'],
            'status' => $data['status'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT)
        ]);

        if(!$updated){
            return redirect()->back()->withInput()->with('errors', $this->adminModel->errors());
        }
        
        return redirect()->back()->with('success', 'Usuário atualizado com sucesso');
    }

    public function destroy($id)
    {
        $verifyAdmin = $this->verifyAdmin($id);

        if($verifyAdmin){
            return redirect()->route('admin.index')->with('error', 'Você não pode ter ações sobre si mesmo.');
        }

        $admin = $this->adminModel->find($id);

        if(!$admin){
            return redirect()->back()->with('error', 'Ocorreu um erro.');
        }

        $this->adminModel->delete($id);

        return redirect()->back()->with('success', 'Excluído com sucesso.');
    }

    public function xlsx()
    {
        $apiUrl = 'https://jsonforexcel.azurewebsites.net/api/Central/Json/Excel';

        $query = $this->filter(); 

        $jsonData = $query->findAll();

        $dataAdmins = array_map(function ($dataAdmin) {
            $status = $dataAdmin->status === '1' ? 'Ativo' : 'Inativo';
            return [
                'Nome' => $dataAdmin->nome,
                'Email' => $dataAdmin->email,
                'Status' => $status,
                'Criado em' => date('d/m/Y', strtotime($dataAdmin->created_at)),
                'Última atualização' => date('d/m/Y', strtotime($dataAdmin->updated_at)),
            ];
        }, $jsonData);

        $admins = json_encode($dataAdmins);

        $requestData = [
            'json' => $admins,
            'sheetName' => 'Admins',
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
                        ->setHeader('Content-Disposition', 'attachment; filename="list_admins.xlsx"')
                        ->setBody($fileContents);
        } else {
            return redirect()->back()->with('error', 'Erro ao extrair planilha');
        }
    }

    private function verifyAdmin($id)
    {
        $session = session();
        $admin = $session->get('admin');

        if(intval($admin->id) === intval($id)){
           return true;  
        } else {
            return false;
        }
    }

    private function filter()
    {
        $search = $this->request->getVar('search');
        $status = $this->request->getVar('status');

        $query = $this->adminModel->orderBy('status', 'DESC')->orderBy('id', 'DESC');
        
        if ($search) {
            $query->groupStart()
                ->like('nome', $search)
                ->orLike('email', $search)
                ->groupEnd();
        }

        if ($status !== null && $status !== '') {
            $query->where('status', $status); 
        }

        return $query;
    }
}
