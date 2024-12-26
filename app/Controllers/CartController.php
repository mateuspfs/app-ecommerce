<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CartModel;
use App\Models\ProductModel;
use CodeIgniter\HTTP\ResponseInterface;

class CartController extends BaseController
{
    protected $cartModel;
    protected $produtoModel;
    protected $request;
    
    public function __construct()
    {
        $this->cartModel = new CartModel();
        $this->produtoModel = new ProductModel();
        $this->request = \Config\Services::request();
    }

    public function index()
    {
        if(!session()->has('user')){
            $produtosCart = session()->get('produtos') ?? [];
        } else {
            $produtosCart = $this->cartModel->where('userId', session('user')->id)->orderBy('created_at', 'DESC')->findAll();
        }
        
        $valorTotal = 0;
        
        if($produtosCart){
            // dd($produtosCart);
            foreach($produtosCart as $produtoCart){
                $produto = $this->produtoModel->select('id, nome, slug, preco, preco_comparativo, img')->find($produtoCart->produtoId);
                $produto->quantidade = $produtoCart->quantidade;

                $valorTotal += $produtoCart->quantidade*$produto->preco;
                $produtos[] = $produto;
            }
        } else {
            $produtos = [];
        }

        $data = [
            'produtos' => $produtos,
            'valorTotal' => $valorTotal
        ];
    
        return view('site/carrinho', $data);
    }

    public function store()
    {
        $response = [
            'success' => null,
            'reason' => null
        ];
        
        if ($this->request->isAJAX()) {
            $productId = $this->request->getPost('produtoId');
            $quantidade = $this->request->getPost('quantidade');
            $product = $this->produtoModel->find($productId); 

            if($quantidade > $product->estoque){
                $response['success'] = false;
                $response['reason'] = "Quantidade pedida maior que o estoque atual do produto!";

                return $this->response->setJSON($response);
            } 

            if (session()->has('user')) {
                $userId = session('user')->id;

                $productExists = $this->cartModel
                                    ->where('userId', $userId)
                                    ->where('produtoId', $productId)
                                    ->first();

                if($productExists){
                    $this->cartModel->update($productExists->id,[
                        'quantidade' => $quantidade 
                    ]);

                    $response['success'] = true;
                    $response['reason'] = "Produto atualizado no carrinho!";
                } else {
                    // dd($userId, $data);

                    $this->cartModel->insert([
                        'userId' => $userId,
                        'produtoId' => $productId,
                        'quantidade' => $quantidade
                    ]);

                    $response['success'] = true;
                    $response['reason'] = "Produto adicionado ao carrinho!";
                }
            }

            $produtos = session()->get('produtos') ?? [];
    
            $productExists = false;
            foreach ($produtos as &$produto) {
                if ($produto->produtoId == $productId) {
                               
                    $produto->quantidade = $quantidade;
                    $productExists = true; 
    
                    $response['success'] = true;
                    $response['reason'] = "Produto atualizado ao carrinho!";
                }
            }
    
            if (!$productExists) {
                $produtos[] = (object)[
                    'produtoId' => $productId,
                    'quantidade' => $quantidade,
                ];

                $response['success'] = true;
                $response['reason'] = "Produto adicionado ao carrinho!";
            }
    
            session()->set('produtos', $produtos);
            return $this->response->setJSON($response);
        }
    }

    public function update() {
        $response = [
            'success' => null,
            'reason' => null
        ];
        
        if ($this->request->isAJAX()) {
            $productId = $this->request->getPost('productId');
            $quantidade = $this->request->getPost('quantity');
            $product = $this->produtoModel->find($productId);   

            if($quantidade > $product->estoque){
                $response['success'] = false;
                $response['reason'] = "Quantidade maior que o estoque atual do produto!";

                return $this->response->setJSON($response);
            } 

            if (session()->has('user')) {
                $userId = session('user')->id;
    
                $productCart = $this->cartModel
                                ->where('userId', $userId)
                                ->where('produtoId', $productId)
                                ->first();
                

                if ($productCart) {
                    
                    $updated = $this->cartModel->update($productCart->id, [
                        'quantidade' => $quantidade
                    ]);
                    
                    if ($updated) {
                        $response['success'] = true;
                    } else {
                        $response['success'] = false;
                        $response['reason'] = "Huve um problema, tente novamente!";    
                    }

                    return $this->response->setJSON($response);
                }
            } else {
                $produtos = session()->get('produtos') ?? [];
                
                foreach ($produtos as &$produto) {
                    if ($produto->produtoId == $productId) {
                        $productCart = $this->cartModel
                                ->where('produtoId', $productId)
                                ->first();
        
                        $produto->quantidade = $quantidade;
                        session()->set('produtos', $produtos);
                        $response['success'] = true;
                        
                        return $this->response->setJSON($response);
                    }
                }
            }
        } else {
            return $this->response->setJSON($response);
        }
    }

    public function destroy($produtoId)
    {
        if(session()->has('user')) {
            $userId = session('user')->id;

            $produtoCart = $this->cartModel
                                ->where('userId', $userId)
                                ->where('produtoId', $produtoId)
                                ->first();
            
            if(!$produtoCart){
                return redirect()->back()->with('rrror', 'Ocorreu um erro, tente novamente');
            } 

            $this->cartModel->delete($produtoCart->id);
        } else {
            $produtos = session()->get('produtos') ?? [];

            $produtos = array_filter($produtos, function($produto) use ($produtoId) {
                return $produto->produtoId != $produtoId;
            });
    
            session()->set('produtos', $produtos);
        }         
    }

    public function getProductsCartCount()
    {
        if (session()->has('user')) {
            $userId = session('user')->id;
            return $this->response->setJSON($this->cartModel->where('userId', $userId)->countAllResults());
        } else {
            $produtos = session()->get('produtos') ?? [];
            return $this->response->setJSON(count($produtos));
        }
    }

    public function cleanCart()
    {
        if (session()->has('user')) {
            $userId = session('user')->id;

            $this->cartModel->where('userId', $userId)->delete();

            return redirect()->back()->with('success', 'Carrinho limpo com sucesso');
        } 
        
        session()->remove('produtos');
        return redirect()->back()->with('success', 'Carrinho limpo com sucesso');
    }
}
