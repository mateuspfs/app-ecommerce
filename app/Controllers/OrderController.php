<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CartModel;
use App\Models\CupomModel;
use App\Models\OrderModel;
use App\Models\PaymentModel;
use App\Models\ProductModel;
use App\Models\ProductsOrderModel;
use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;
use DateTime;

class OrderController extends BaseController
{
    //Payments
    protected $paymentController;

    //Models
    protected $orderModel;
    protected $productModel;
    protected $productOrderModel;
    protected $userModel;
    protected $cupomModel;
    protected $cartModel;
    protected $paymentModel;
    protected $request;

    public function __construct()
    {
        $this->paymentController = new PaymentController;
        $this->orderModel = new OrderModel();
        $this->productModel = new ProductModel();
        $this->productOrderModel = new ProductsOrderModel();
        $this->userModel = new UserModel();
        $this->cupomModel = new CupomModel();
        $this->cartModel = new CartModel();
        $this->paymentModel = new PaymentModel();
        $this->request = \Config\Services::request();
    }

    public function index()
    {
        $pagination = service('pager');

        $query = $this->filter();

        if ($query->countAllResults(false) < 1) {
            return redirect()->back()->with('error', 'Você ainda não tem pedidos!');
        }

        $orders = $query->findAll();
        
        foreach($orders as $order){
            if (!($order->valor == 0.00 || $order->response == null) && $order->status === 'Aguardando Pagamento') {
                // dd($order);
                $this->paymentController->consultaPayment($order->codigo);
            }
        }

        $data = [
            'orders' => $orders,
            'pagination' => $pagination
        ];

        // dd($orders);
        return view('site/orders', $data);
    }

    public function integra($codigo)
    {
        $userId = session('user')->id;
        $order = $this->orderModel
            ->select('pedidos.*, cupons.codigo as cupom, pagamentos.status as statusPag, pagamentos.method as method, pagamentos.response_pagseguro as response')
            ->join('cupons', 'cupons.id = pedidos.cupomId', 'left')
            ->join('pagamentos', 'pedidos.id = pagamentos.pedidoId', 'left')
            ->where('pedidos.codigo', $codigo)
            ->where('pedidos.userId', $userId)
            ->first();

        if (!$order) {
            return redirect()->route('order.index')->with('error', 'Não foi possível abrir a integra do pedido!');
        }

        $user = $this->userModel->find($userId);
        $cupom = $this->cupomModel->find($order->cupomId);
        $productsOrder = $this->productOrderModel->where('pedidoId', $order->id)->findAll();
        $products = [];
        $subtotal = 0;

        foreach ($productsOrder as $productOrder) {
            $productFind = $this->productModel->find($productOrder->produtoId);
            $productFind->quantidade = $productOrder->quantidade;
            $subtotal += $productFind->quantidade * $productFind->preco;
            $products[] = $productFind;
        }

        $valorTotal = $order->valor;

        $data = [
            'order' => $order,
            'user' => $user,
            'cupom' => $cupom,
            'products' => $products,
            'subtotal' => $subtotal,
            'valorTotal' => $valorTotal
        ];

        return View('site/integra_order', $data);
    }

    public function admin_index()
    {
        $pagination = service('pager');

        $query = $this->filter_admin();

        if ($query->countAllResults(false) < 1) {
            return redirect()->route('admin.order.index')->with('error', 'Sem resultados para sua busca');
        }

        $orders = $query->paginate(10);

        foreach($orders as $order){
            if (!($order->valor == 0.00 || $order->response == null) && $order->status === 'Aguardando Pagamento') {
                $this->paymentController->consultaPayment($order->codigo);
            }
        }

        $data = [
            'orders' => $orders,
            'pagination' => $pagination
        ];

        return view('admin/orders', $data);
    }

    public function admin_integra($codigo)
    {
        $order = $this->orderModel
            ->select('pedidos.*, cupons.codigo as cupom, pagamentos.status as statusPag, pagamentos.method as method, pagamentos.response_pagseguro as response')
            ->join('cupons', 'cupons.id = pedidos.cupomId', 'left')
            ->join('pagamentos', 'pedidos.id = pagamentos.pedidoId', 'left')
            ->where('pedidos.codigo', $codigo)
            ->first();

        $user = $this->userModel->find($order->userId);
        $cupom = $this->cupomModel->find($order->cupomId);
        $productsOrder = $this->productOrderModel->where('pedidoId', $order->id)->findAll();
        $products = [];
        $subtotal = 0;

        foreach ($productsOrder as $productOrder) {
            $productFind = $this->productModel->find($productOrder->produtoId);
            $productFind->quantidade = $productOrder->quantidade;
            $subtotal += $productFind->quantidade * $productFind->preco;
            $products[] = $productFind;
        }

        $valorTotal = $order->valor;

        $data = [
            'order' => $order,
            'user' => $user,
            'cupom' => $cupom,
            'products' => $products,
            'subtotal' => $subtotal,
            'valorTotal' => $valorTotal
        ];

        return View('admin/integra_order', $data);
    }

    public function resumeOrder()
    {
        if (!session()->has('user')) {
            session()->set('redirect_to', route_to('order.resume'));
            return redirect()->route('auth.login')->with('error', 'Faça login para continuar com o pedido');
        }

        $produtosCart = $this->cartModel->where('userId', session('user')->id)->orderBy('created_at', 'DESC')->findAll();

        $valorTotal = 0;

        if ($produtosCart) {
            foreach ($produtosCart as $produtoCart) {
                $produto = $this->productModel->select('id, nome, slug, preco, preco_comparativo, img')->find($produtoCart->produtoId);
                $produto->quantidade = $produtoCart->quantidade;

                $valorTotal += $produtoCart->quantidade * $produto->preco;
                $produtos[] = $produto;
            }
        } else {
            return redirect()->back()->with('error', 'Não há produtos no carrinho para realizar o pedido!');
        }

        $user = $this->userModel->find(session('user')->id);

        $data = [
            'produtos' => $produtos,
            'valorTotal' => $valorTotal,
            'credito' => $user->credito
        ];

        return view('site/resumoPedido', $data);
    }

    public function store()
    {
        $paymentMethod = $this->request->getPost('paymentMethod');
        $cryptedCard = $this->request->getPost('crypted_card');

        $validationRules = [
            'cep' => 'required',
            'rua' => 'required',
            'numero' => 'required',
            'cidade' => 'required',
            'uf' => 'required|exact_length[2]',
            'complemento' => 'permit_empty',
            'paymentMethod' => 'required|in_list[credit-card,pix,boleto]',
        ];

        $validationMessages = [
            'cep' => [
                'required' => 'O campo CEP é obrigatório.'
            ],
            'rua' => [
                'required' => 'O campo Rua é obrigatório.'
            ],
            'numero' => [
                'required' => 'O campo Número é obrigatório.'
            ],
            'cidade' => [
                'required' => 'O campo Cidade é obrigatório.'
            ],
            'uf' => [
                'required' => 'O campo Estado (UF) é obrigatório.',
                'exact_length' => 'O campo Estado (UF) deve ter exatamente 2 caracteres.'
            ],
            'paymentMethod' => [
                'required' => 'O método de pagamento é obrigatório.',
                'in_list' => 'O método de pagamento selecionado é inválido.'
            ],
        ];

        if ($paymentMethod === 'credit-card') {
            $validationRules = array_merge($validationRules, [
                'crypted_card' => 'required',
            ]);

            $validationMessages = array_merge($validationMessages, [
                'crypted_card' => [
                    'required' => 'Algo deu errado na validação do seu cartão, tente novamente!'
                ],
            ]);
        }

        if (!$this->validate($validationRules, $validationMessages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = $this->request->getPost();        
        $userId = session('user')->id;
        

        $produtosCart = $this->cartModel->where('userId', $userId)->orderBy('created_at', 'DESC')->findAll();

        if (empty($produtosCart)) {
            return redirect()->route('index')->with('error', 'ocorreu um erro com a criação do pedido, confira seus itens');
        }

        $valorTotal = 0;

        $endereco = [
            'cep' => $data['cep'],
            'rua' => $data['rua'],
            'bairro' => $data['bairro'],
            'numero' => $data['numero'],
            'cidade' => $data['cidade'],
            'uf' => $data['uf'],
            'complemento' => $data['complemento']
        ];

        if ($produtosCart) {
            foreach ($produtosCart as $produtoCart) {
                $produto = $this->productModel->find($produtoCart->produtoId);

                $valorTotal += $produtoCart->quantidade * $produto->preco;
            }
        }

        if (!empty($data['cupom'])) {
            $cupom = $this->cupomModel->where('codigo', $data['cupom'])->first();

            if ($cupom && $cupom->status !== '0') {
                $timestamp = new DateTime();
                $expired_at = new DateTime($cupom->expired_at);
                $currentDate = $timestamp->format('Y-m-d H:i:s');
                $cupomExpiredDate = $expired_at->format('Y-m-d H:i:s');

                if ($currentDate < $cupomExpiredDate) {
                    if ($cupom->qt_disponivel > 0) {
                        $countUses = $this->orderModel
                            ->where('userId', session('user')->id)
                            ->where('cupomId', $cupom->id)
                            ->countAllResults();

                        if (!($countUses >= $cupom->qt_cliente)) {
                            if ($cupom->tipo === 'p') {
                                if (($valorTotal - ($valorTotal * intval($cupom->desconto) / 100)) < 0) {
                                    $valorTotal = 0;
                                } else {
                                    $valorTotal = $valorTotal - ($valorTotal * intval($cupom->desconto) / 100);
                                }
                                $cupomId = $cupom->id;
                            } elseif ($cupom->tipo === 'f') {
                                if (($valorTotal - $cupom->desconto) < 0) {
                                    $valorTotal = 0;
                                } else {
                                    $valorTotal = $valorTotal - $cupom->desconto;
                                }
                                $cupomId = $cupom->id;
                            }
                        }
                    }
                    // else {
                    //     dd('quantidade disponivel acabou', $cupom);
                    // }
                }
                // else {
                //     dd('cupom expirou', $cupom);
                // }
            }
            // else {
            //     dd('cupom n existe ou inativo', $cupom);
            // }
        }

        $user = $this->userModel->find($userId);

        if($data['credit-usage'] > 0){
            if($data['credit-usage'] > $user->credito){
                return redirect()->back('')->with('error', 'Crédito usado maior que o que você possui!');
            } else {
                $valorTotal = $valorTotal - $data['credit-usage'];
                $creditUser = $user->credito - $data['credit-usage']; 
                $this->userModel->update($user->id, [
                    'credito' => $creditUser
                ]);
            }
        } 

        $dataOrder = [
            'codigo' => $this->gerarCodigo(),
            'userId' => session('user')->id,
            'cupomId' => isset($cupomId) ? $cupomId : null,
            'endereco' => json_encode($endereco, JSON_UNESCAPED_UNICODE),
            'valor' => $valorTotal,
            'status' => 'Aguardando Pagamento'
        ];

        $inserted = $this->orderModel->insert($dataOrder);

        if (!$inserted) {
            return redirect()->back()->withInput()->with('errors', $this->orderModel->errors());
        }

        $orderInserted = $this->orderModel->find($this->orderModel->insertID());

        if ($orderInserted->cupomId) {
            $cupomUsed = $this->cupomModel->find($orderInserted->cupomId);

            $this->cupomModel->update($cupomUsed->id, [
                'qt_usada' => $cupomUsed->qt_usada + 1,
                'qt_disponivel' => $cupomUsed->qt_disponivel - 1,
            ]);
        }

        foreach ($produtosCart as $productSell) {
            $product = $this->productModel->find($productSell->produtoId);

            $this->productOrderModel->insert([
                'pedidoId' => $orderInserted->id,
                'produtoId' => $productSell->produtoId,
                'quantidade' => $productSell->quantidade
            ]);

            $this->productModel->update($product->id, [
                'estoque' => $product->estoque - $productSell->quantidade
            ]);
        }

        $this->cartModel->where('userId', $userId)->delete();

        $this->paymentController->payOrder($orderInserted->id, $paymentMethod, $cryptedCard);

        if ($paymentMethod === 'credit-card') {
            return redirect()->route('order.index')->with('success', 'Pedido feito com sucesso!');
        }

        $url_to = url_to('payment.pay', $orderInserted->codigo);

        return redirect()->to($url_to)->with('success', 'Pedido criado com sucesso, faça o pagamento!');
    }

    public function setShipping($id)
    {
        $order = $this->orderModel->where('codigo', $id)->first();

        if($order->status === 'Pago'){
            $this->orderModel->update($order->id,[
                'status' => 'Em entrega'
            ]);

            return redirect()->route('admin.order.index')->with('success', 'Status atualizado com sucesso!');
        }
        
        return redirect()->route('admin.order.index')->with('error', 'Ocorreu um erro!');
    }

    public function destroy($id)
    {
        $order = $this->orderModel->find($id);

        if (!$order) {
            return redirect()->route('admin.order.index')->with('error', 'Ocorreu um erro.');
        }

        $this->orderModel->delete($id);

        return redirect()->route('admin.order.index')->with('success', 'Pedido excluído com sucesso');
    }

    public function xlsx()
    {
        $apiUrl = 'https://jsonforexcel.azurewebsites.net/api/Central/Json/Excel';

        $query = $this->filter_admin();

        $jsonData = $query->findAll();

        $dataOrders = array_map(function ($dataOrder) {
            $user = $this->userModel->find($dataOrder->userId);
            $cupom = $this->cupomModel->find($dataOrder->cupomId);
            $endereco = json_decode($dataOrder->endereco);

            return [
                'Código Pedido' => $dataOrder->codigo,
                'Usuário' => $user->nome,
                'Email Cliente' => $user->email,
                'Telefone Cliente' => mask_telefone($user->telefone),
                'Cupom' => $dataOrder->cupomId ? $cupom->codigo : 'N/Utilizado',
                'Desconto' => $dataOrder->cupomId ? ($cupom->tipo === 'f') ? 'R$' . $cupom->desconto : intval($cupom->desconto) . '%' : 'N/Utilizado',
                'Valor' => 'R$ ' . mask_valor($dataOrder->valor),
                'Rua' => $endereco->rua,
                'Bairro' => $endereco->bairro,
                'Cidade' => $endereco->cidade,
                'UF' => $endereco->uf,
                'Status' => $dataOrder->status,
                'Criado em' => date('d/m/Y', strtotime($dataOrder->created_at)),
                'Última atualização' => date('d/m/Y', strtotime($dataOrder->updated_at)),
                'Excluído em' => $dataOrder->deleted_at === null ? 'N/Excluido' : date('d/m/Y', strtotime($dataOrder->deleted_at))
            ];
        }, $jsonData);

        $orders = json_encode($dataOrders);

        $requestData = [
            'json' => $orders,
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
                ->setHeader('Content-Disposition', 'attachment; filename="list_orders.xlsx"')
                ->setBody($fileContents);
        } else {
            return redirect()->back()->with('error', 'Erro ao extrair planilha');
        }
    }

    public function setReceived($id)
    {
        $order = $this->orderModel->where('codigo', $id)->first();

        if($order->status === 'Em entrega' && $order->userId === session('user')->id){
            $this->orderModel->update($order->id,[
                'status' => 'Concluído'
            ]);

            return redirect()->route('order.index')->with('success', 'Pedido concluído com sucesso!');
        }
        
        return redirect()->route('order.index')->with('error', 'Ocorreu um erro!');
    }

    private function gerarCodigo()
    {
        $caracteres = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $codigo = '';

        do {
            $codigo = '';
            for ($i = 0; $i < 8; $i++) {
                $codigo .= $caracteres[rand(0, strlen($caracteres) - 1)];
            }
            $codigoExists = $this->orderModel->where('codigo', $codigo)->first();
        } while ($codigoExists);

        return $codigo;
    }

    private function filter_admin()
    {
        $search = $this->request->getVar('search');
        $status = $this->request->getVar('status');

        $query = $this->orderModel
            ->select('pedidos.*, users.nome as nm_user, cupons.codigo as cupom, pagamentos.response_pagseguro as response')
            ->join('users', 'users.id = pedidos.userId')
            ->join('cupons', 'cupons.id = pedidos.cupomId')
            ->join('pagamentos', 'pedidos.id = pagamentos.pedidoId', 'left')
            ->orderBy('pedidos.id', 'DESC')
            ->orderBy('status', 'ASC');

        if ($search !== null && $search !== '') {
            $query->where('pedidos.codigo', $search);
        }

        if ($status !== null && $status !== '') {
            $query->where('pedidos.status', $status);
        }

        return $query;
    }

    private function filter()
    {
        $search = $this->request->getVar('search');
        $status = $this->request->getVar('status');

        $query = $this->orderModel
            ->select('pedidos.*, cupons.codigo as cupom, pagamentos.response_pagseguro as response')
            ->join('cupons', 'cupons.id = pedidos.cupomId', 'left')
            ->join('pagamentos', 'pedidos.id = pagamentos.pedidoId', 'left')
            ->where('userId', session('user')->id)
            ->orderBy('pedidos.id', 'DESC')
            ->orderBy('pedidos.status', 'ASC');

        if (!empty($status)) {
            $query->where('pedidos.status', $status);
        }

        if (!empty($search)) {
            $query->where('pedidos.codigo', $search);
        }

        return $query;
    }
}
