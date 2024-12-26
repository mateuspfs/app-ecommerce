<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;

class UserController extends BaseController
{
    protected $userModel;
    protected $request;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->request = \Config\Services::request();
    }

    public function index()
    {
        $pagination = service('pager');
    
        $query = $this->filter(); 

        if($query->countAllResults(false) < 1){
            return redirect()->route('user.index')->with('error', 'Sem resultados para sua busca');
        }

        // dd($query->paginate(20), $query);
        $data = [
            'users' => $query->paginate(10),
            'pagination' => $pagination
        ];
        
        return view('admin/users', $data);
    }


    public function create()
    {
        return View('site/cadastro');
    }

    public function store()
    {
        $validationRules = [
            'email' => 'required|valid_email|is_unique[users.email]',
            'cpf' => 'required|valid_cpf|is_unique[users.cpf]|max_length[14]',
            'password' => 'required',
            'cpassword' => 'required|matches[password]'
        ];
    
        $validationMessages = [
            'email' => [
                'required' => 'O email é obrigatório!',
                'valid_email' => 'O email deve ser válido!',
                'is_unique' => 'Email já cadastrado no sistema!',
            ],
            'cpf' => [
                'required' => 'O CPF é obrigatório!',
                'is_unique' => 'Já existe um usuário com este CPF cadastrado!',
                'max_length' => 'CPF informado muito grande!',
                'valid_cpf' => 'CPF não é válido!'
            ],
            'password' => [
                'required' => 'A senha é obrigatória!'
            ],
            'cpassword' => [
                'required' => 'A confirmação de senha é obrigatória!',
                'matches' =>'Senhas não coincidem!'
            ]
        ];
        
        if (!$this->validate($validationRules, $validationMessages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = $this->request->getPost();
        $data['cpf'] = preg_replace("/[^0-9]/", "", $data['cpf']);
        $data['telefone'] = preg_replace("/[^0-9]/", "", $data['telefone']);
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        $inserted = $this->userModel->insert($data);      
          
        if(!$inserted){
            return redirect()->back()->withInput()->with('errors', $this->userModel->errors());
        }

        if(session('admin')){
            return redirect()->back()->with('success', 'Usuário adcionado com sucesso');
        }

        return redirect()->route('auth.login')->with('success', 'Cadastrado com sucesso, faça login!');
    }

    public function edit()
    {
        $user = $this->userModel->find(session('user')->id);
        $data = [
            'user' => $user
        ];
        return view('site/edit_user', $data);
    }

    public function update($id)
    {
        $validationRules = [
            'email' => 'required|valid_email|is_unique[users.email,users.id,' . $id .']',
        ];
    
        $validationMessages = [
            'email' => [
                'required' => 'O email é obrigatório',
                'valid_email' => 'O email deve ser válido',
                'is_unique' => 'Email já cadastrado no sistema',
            ],
        ];
    
        if (!$this->validate($validationRules, $validationMessages)) {
            return redirect()->route('user.index')->with('errors', $this->validator->getErrors());
        }

        $data = $this->request->getPost();
        // dd($data);
        if($data['password'] xor $data['cpassword']){
            return redirect()->back()->with('error', 'Prencha a senha e confimação de senha para atualizar corretamente.');
        }

        $user = $this->userModel->find($id);

        if(!$user){
            return redirect()->back()->with('error', 'Ocorreu um erro ao atualizar usuário.');
        }

        $data['telefone'] = preg_replace("/[^0-9]/", "", $data['telefone']);

        if(empty($data['password']) && empty($data['cpassword'])){
            $updated = $this->userModel->update($id, [
                'nome' => $data['nome'],
                'email' => $data['email'],
                'telefone' => $data['telefone'],
            ]);
            
            if(!$updated){
                return redirect()->back()->withInput()->with('errors', $this->userModel->errors());
            }

            return redirect()->back()->with('success', 'Usuário atualizado com sucesso');
        } else {
            if($data['password'] == $data['cpassword']){
                $updated = $this->userModel->update($id, [
                    'nome' => $data['nome'],
                    'email' => $data['email'],
                    'telefone' => $data['telefone'],
                    'password' => password_hash($data['password'], PASSWORD_DEFAULT)
                ]);
                
                if(!$updated){
                    return redirect()->back()->withInput()->with('errors', $this->userModel->errors());
                }
    
                return redirect()->back()->with('success', 'Usuário atualizado com sucesso');
            } else {
                return redirect()->back()->withInput()->with('error', 'Senhas informadas não correspondem.');
            }
        }
    }

    public function admin_update($id)
    {
        $validationRules = [
            'email' => 'required|valid_email|is_unique[users.email,users.id,' . $id .']',
            'cpf' => 'required|max_length[14]|valid_cpf|is_unique[users.cpf,users.id,' . $id .']'
        ];
    
        $validationMessages = [
            'email' => [
                'required' => 'O email é obrigatório',
                'valid_email' => 'O email deve ser válido',
                'is_unique' => 'Email já cadastrado no sistema',
            ],
            'cpf' => [
                'required' => 'O CPF é obrigatório',
                'is_unique' => 'Já existe um usuário com este CPF cadastrado',
                'valid_cpf' => 'CPF não é válido',
                'max_length' => 'CPF informado muito grande!',
            ],
        ];
    
        if (!$this->validate($validationRules, $validationMessages)) {
            return redirect()->route('user.index')->with('errors', $this->validator->getErrors());
        }

        $data = $this->request->getPost();
        // dd($data);

        if($data['password'] xor $data['cpassword']){
            return redirect()->back()->with('error', 'Prencha a senha e confimação de senha para atualizar corretamente.');
        }

        $user = $this->userModel->find($id);

        if(!$user){
            return redirect()->back()->with('error', 'Ocorreu um erro ao atualizar usuário.');
        }

        $data['cpf'] = preg_replace("/[^0-9]/", "", $data['cpf']);
        $data['telefone'] = preg_replace("/[^0-9]/", "", $data['telefone']);

        if(empty($data['password']) && empty($data['cpassword'])){
            $updated = $this->userModel->update($id, [
                'nome' => $data['nome'],
                'email' => $data['email'],
                'cpf' => $data['cpf'],
                'telefone' => $data['telefone'],
                'status' => $data['status']
            ]);
            
            if(!$updated){
                return redirect()->back()->withInput()->with('errors', $this->userModel->errors());
            }

            return redirect()->back()->with('success', 'Usuário atualizado com sucesso');
        } else {
            if($data['password'] == $data['cpassword']){
                $updated = $this->userModel->update($id, [
                    'nome' => $data['nome'],
                    'email' => $data['email'],
                    'cpf' => $data['cpf'],
                    'telefone' => $data['telefone'],
                    'status' => $data['status'],
                    'password' => password_hash($data['password'], PASSWORD_DEFAULT)
                ]);
                
                if(!$updated){
                    return redirect()->back()->withInput()->with('errors', $this->userModel->errors());
                }
    
                return redirect()->back()->with('success', 'Usuário atualizado com sucesso');
            } else {
                return redirect()->back()->withInput()->with('error', 'Senhas informadas não correspondem.');
            }
        }
    }

    public function destroy($id)
    {
        $user = $this->userModel->find($id);

        if(!$user){
            return redirect()->back()->with('error', 'Ocorreu um erro.');
        }

        $this->userModel->delete($id);

        return redirect()->back()->with('success', 'Usuário excluido com sucesso');
    }

    public function xlsx()
    {
        $apiUrl = 'https://jsonforexcel.azurewebsites.net/api/Central/Json/Excel';

        $query = $this->filter(); 

        $jsonData = $query->withDeleted()->findAll();

        $dataUsers = array_map(function ($dataUser) {
            $status = $dataUser->status === '1' ? 'Ativo' : 'Inativo';
            return [
                'Id' => $dataUser->id,
                'Nome' => $dataUser->nome,
                'Email' => $dataUser->email,
                'Cpf' => mask_cpf($dataUser->cpf),
                'Telefone' => mask_telefone($dataUser->telefone),
                'Status' => $status,
                'Criado em' => date('d/m/Y', strtotime($dataUser->created_at)),
                'Última atualização' => date('d/m/Y', strtotime($dataUser->updated_at)),
                'Excluído em' => ($dataUser->deleted_at) ? date('d/m/Y', strtotime($dataUser->deleted_at)) : '',
            ];
        }, $jsonData);

        $Users = json_encode($dataUsers);

        $requestData = [
            'json' => $Users,
            'sheetName' => 'Users',
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
                        ->setHeader('Content-Disposition', 'attachment; filename="list_Users.xlsx"')
                        ->setBody($fileContents);
        } else {
            return redirect()->back()->with('error', 'Erro ao extrair planilha');
        }
    }

    private function filter()
    {
        $search = $this->request->getVar('search');
        $status = $this->request->getVar('status');
        $cpf = preg_replace("/[^0-9]/", "", $this->request->getVar('cpf'));

        $query = $this->userModel->orderBy('status', 'DESC')->orderBy('id', 'DESC');
        
        if ($search !== null && $search !== '') {
            $query->groupStart()
                ->like('nome', $search)
                ->orLike('email', $search)
                ->groupEnd();
        }

        if ($cpf !== null && $cpf !== '') {
            $query->where('cpf', $cpf);
        }

        if ($status !== null && $status !== '') {
            $query->where('status', $status); 
        }

        return $query;
    }
}
