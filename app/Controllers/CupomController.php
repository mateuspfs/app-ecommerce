<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CupomModel;
use App\Models\OrderModel;
use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;
use DateTime;

class CupomController extends BaseController
{
    protected $cupomModel;
    protected $userModel;
    protected $orderModel;
    protected $request;

    public function __construct()
    {
        $this->cupomModel = new CupomModel();
        $this->userModel = new UserModel();
        $this->orderModel = new OrderModel();
        $this->request = \Config\Services::request();
    }

    public function index()
    {
        $pagination = service('pager');

        $query = $this->filter(); 

        if($query->countAllResults(false) < 1){
            return redirect()->route('cupom.index')->with('error', 'Sem resultados para sua busca');
        }

        $data = [
            'cupons' => $query->paginate(10),
            'pagination' => $pagination
        ];
        
        return view('admin/cupons', $data);
    }

    public function store()
    {
        $validationRules = [
            'codigo' => 'required|max_length[10]|is_unique[cupons.codigo]',
        ];

        $validationMessages = [
            'codigo' => [
                'required' => 'O código é obrigatório.',    
                'max_length' => 'O código não pode ter mais de 10 caracteres.',
                'is_unique' => 'O código já existe no sistema.'
            ]   
        ];

        if (!$this->validate($validationRules, $validationMessages)) {
            return redirect()->route('cupom.index')->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = $this->request->getPost();
        
        $inserted = $this->cupomModel->insert($data);

        if(!$inserted){
            return redirect()->back()->withInput()->with('errors', $this->cupomModel->errors());
        }

        return redirect()->back()->with('success', 'Cadastrado com sucesso!');
    }

    public function edit()
    {
        
    }

    public function update($id)
    {
        $validationRules = [
            'codigo' => 'required|max_length[10]|is_unique[cupons.codigo,cupons.id,'.$id.']',
        ];

        $validationMessages = [
            'codigo' => [
                'required' => 'O código é obrigatório.',
                'max_length' => 'O código não pode ter mais de 10 caracteres.',
                'is_unique' => 'O código já existe no sistema.'
            ]
        ];

        if (!$this->validate($validationRules, $validationMessages)) {
            return redirect()->route('cupom.index')->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = $this->request->getPost();

        $cupom = $this->cupomModel->find($id);

        if(!$cupom){
            return redirect()->back()->with('error', 'Ocorreu um erro ao atualizar categoria.');
        }
        
        $updated = $this->cupomModel->update($id, [
            'nome' => $data['nome'],
            'codigo' => $data['codigo'],
            'tipo' => $data['tipo'],
            'desconto' => $data['desconto'],
            'qt_disponivel' => $data['qt_disponivel'],
            'qt_cliente' => $data['qt_cliente'],
            'expired_at' => $data['expired_at'],
            'status' => $data['status'],
        ]);
        
        if(!$updated){
            return redirect()->back()->withInput()->with('errors', $this->cupomModel->errors());
        }

        return redirect()->back()->with('success', 'Cupom atualizado com sucesso');
    }

    public function destroy($id)
    {
        $cupom = $this->cupomModel->find($id);

        if(!$cupom){
            return redirect()->back()->with('error', 'Ocorreu um erro.');
        }

        $this->cupomModel->delete($id);

        return redirect()->back()->with('success', 'Cupom excluído com sucesso');
    }

    public function xlsx()
    {
        $apiUrl = 'https://jsonforexcel.azurewebsites.net/api/Central/Json/Excel';

        $query = $this->filter(); 

        $jsonData = $query->findAll();

        $dataCupons = array_map(function ($dataCupom) {
            $status = $dataCupom->status === '1' ? 'Ativo' : 'Inativo';
            $tipo = $dataCupom->tipo === 'f' ? 'Fixo' : 'Porcentagem';
            $desconto = $dataCupom->tipo === 'p' ?  intval($dataCupom->desconto) . '%' :  'R$ ' . $dataCupom->desconto;
            return [
                'Nome' => $dataCupom->nome,
                'Código' => $dataCupom->codigo,
                'Tipo' => $tipo,
                'Desconto' => $desconto,
                'Quantidade Disponível' => $dataCupom->qt_disponivel,
                'Quantidade p\cliente' => $dataCupom->qt_cliente,
                'Validade em' => date('d/m/Y', strtotime($dataCupom->expired_at)),
                'Status' => $status,
                'Criado em' => date('d/m/Y', strtotime($dataCupom->created_at)),
                'Última atualização' => date('d/m/Y', strtotime($dataCupom->updated_at)),
                'Excluído em' => $dataCupom->deleted_at === null ? 'N\Excluido' : date('d/m/Y', strtotime($dataCupom->deleted_at))
            ];
        }, $jsonData);

        $cupons = json_encode($dataCupons);

        $requestData = [
            'json' => $cupons,
            'sheetName' => 'CuponsList',
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
                        ->setHeader('Content-Disposition', 'attachment; filename="list_cupons.xlsx"')
                        ->setBody($fileContents);
        } else {
            return redirect()->back()->with('error', 'Erro ao extrair planilha');
        }
    }

    private function filter()
    {
        $search = $this->request->getVar('search');
        $status = $this->request->getVar('status');
        $tipo = $this->request->getVar('tipo');

        $query = $this->cupomModel->orderBy('status', 'DESC')->orderBy('expired_at', 'ASC');
        
        if ($search !== null && $search !== '') {
            $query->groupStart()
                ->like('nome', $search)
                ->orLike('codigo', $search)
                ->groupEnd();
        }
                
        if ($status !== null && $status !== '') {
            $query->where('status', $status); 
        }

        if ($tipo !== null && $tipo !== '') {
            $query->where('tipo', $tipo); 
        }

        return $query;
    }

    public function verifyCupom()
    {
        $data = $this->request->getPost();
        $cupom = $this->cupomModel->where('codigo', $data['cupom'])->first();

        $result = [
            'valid' => null,
            'reason' => null,
            'type' => null,
            'discount' => null,
        ];

        // dd(new DateTime());

        if(!$cupom || $cupom->status === '0'){
            $result['valid'] = false;
            $result['reason'] = 'Cupom informado não existe!';
        } else {
            $timestamp = new DateTime();
            $expired_at = new DateTime($cupom->expired_at);
            $currentDate = $timestamp->format('Y-m-d H:i:s');
            $cupomExpiredDate = $expired_at->format('Y-m-d H:i:s');

            if($currentDate < $cupomExpiredDate){ 
                if($cupom->qt_disponivel > 0){
                    $countUses = $this->orderModel
                            ->where('userId', session('user')->id)
                            ->where('cupomId', $cupom->id)
                            ->countAllResults(); 
                    
                    if($countUses >= $cupom->qt_cliente){
                        $result['valid'] = false;
                        $result['reason'] = 'Você já atingiu o máximo de usos para esse cupom!';
                    } else {
                        $result['valid'] = true;
                        $result['type'] = $cupom->tipo;
                        $result['discount'] = $cupom->desconto;
                    }
                } else {
                    $result['valid'] = false;
                    $result['reason'] = 'Cupom já atingiu a máxima quantidade de usos!';
                }
            } else {
                $result['valid'] = false;
                $result['reason'] = 'Cupom expirado!';
            }
        }

        // dd($result);
        return $this->response->setJSON($result);
    }
}
