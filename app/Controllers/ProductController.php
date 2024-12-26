<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CategoryModel;
use App\Models\ImagensProdutoModel;
use App\Models\ProductModel;
use CodeIgniter\HTTP\ResponseInterface;
use DateTime;

class ProductController extends BaseController
{
    protected $productModel;
    protected $categoryModel;
    protected $imagensProdutoModel;
    protected $request;

    public function __construct()
    {
        $this->productModel = new ProductModel();
        $this->categoryModel = new CategoryModel();
        $this->imagensProdutoModel = new ImagensProdutoModel();
        $this->request = \Config\Services::request();
    }

    public function index()
    {
        $pagination = service('pager');

        $query = $this->filter();

        if ($query->countAllResults(false) < 1) {
            return redirect()->route('product.index')->with('error', 'Sem resultados para sua busca');
        }

        $produtos = $query->paginate(8);

        $data = [
            'categorias' => $this->categoryModel->findAll(),
            'produtos' => $produtos,
            'pagination' => $pagination
        ];

        return view('site/produtos', $data);
    }

    public function integra($slug)
    {
        $product = $this->productModel->where('slug', $slug)->first();
        $galery = $this->imagensProdutoModel->where('produtoId', $product->id)->findAll();
        $recommended = $this->productModel
            ->where('categoriaId', $product->categoriaId)
            ->where('id !=', $product->id)
            ->orderBy('descricao', 'RAMDOM')
            ->limit(3)
            ->findAll();

        $data = [
            'produto' => $product,
            'galery' => $galery,
            'recommended' => $recommended
        ];

        // dd($recommended);
        // dd($data);

        return View('site/integra', $data);
    }

    public function admin_index()
    {
        $pagination = service('pager');

        $query = $this->filter_admin();

        if ($query->countAllResults(false) < 1) {
            return redirect()->route('admin.product.index')->with('error', 'Sem resultados para sua busca');
        }

        $produtos = $query->paginate(10);

        foreach ($produtos as &$produto) {
            $produto->galeria = $this->getImagesByProductId($produto->id);
        }
        
        $data = [
            'categorias' => $this->categoryModel->findAll(),
            'produtos' => $produtos,
            'pagination' => $pagination
        ];

        return view('admin/produtos', $data);
    }

    public function store()
    {
        $validationRules = [
            'img' => 'uploaded[img]|is_image[img]|ext_in[img,jpg,png,jpeg]',
            'categoriaId' => 'required|integer|is_not_unique[categorias.id]',
        ];

        $validationMessages = [
            'img' => [
                'uploaded' => 'A imagem é obrigatória.',
                'is_image' => 'O arquivo enviado não é uma imagem.',
                'ext_in' => 'Essa extensão de imagem não é aceita.'
            ]
        ];

        if (!$this->validate($validationRules, $validationMessages)) {
            return redirect()->route('admin.product.index')->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = $this->request->getPost();
        $imagesForm = $this->request->getFiles();

        $data['img'] = $this->uploadImage($imagesForm['img']);
        $data['preco'] = str_replace('.', '', $data['preco']);
        $data['preco'] = str_replace(',', '.', $data['preco']);
        
        if($data['preco_comparativo'] === '')  {
            $data['preco_comparativo'] = null;
        } else {
            $data['preco_comparativo'] = str_replace('.', '', $data['preco_comparativo']);
            $data['preco_comparativo'] = str_replace(',', '.', $data['preco_comparativo']);
        }

        $data['slug'] = url_title($data['nome'], '-', true);
        $slugExists = $this->productModel->where('slug', $data['slug'])->first();

        if ($slugExists) {
            $suffix = 1;
            $originalSlug = $data['slug'];

            while ($this->productModel->where('slug', $data['slug'])->first()) {
                $data['slug'] = $originalSlug . '-' . $suffix;
                $suffix++;
            }
        }

        $inserted = $this->productModel->insert($data);

        if (!$inserted) {
            return redirect()->route('admin.product.index')->withInput()->with('errors', $this->productModel->errors());
        }

        $validImages = [];

        if (isset($imagesForm['imagens'])) {
            $uploadedImages = $imagesForm['imagens'];
            foreach ($uploadedImages as $image) {
                if ($image->isValid()) {
                    $validImages[] = $image;
                }
            }
        }

        if (!empty($validImages)) {
            $this->uploadImageGalery($imagesForm['imagens'], $this->productModel->insertID());
        }

        return redirect()->route('admin.product.index')->with('success', 'Cadastrado com sucesso!');
    }

    public function edit()
    {
    }

    public function update($id)
    {
        $data = $this->request->getPost();
        $product = $this->productModel->find($id);

        $data['preco'] = str_replace('.', '', $data['preco']);
        $data['preco'] = str_replace(',', '.', $data['preco']);

        if($data['preco_comparativo'] === '' || $data['preco_comparativo'] === '0,00')  {
            $data['preco_comparativo'] = null;
        } else {
            $data['preco_comparativo'] = str_replace('.', '', $data['preco_comparativo']);
            $data['preco_comparativo'] = str_replace(',', '.', $data['preco_comparativo']);
        }

        if (!$product) {
            return redirect()->route('admin.product.index')->with('error', 'Ocorreu um erro ao atualizar produto.');
        }
        // dd($data);

        $updated = $this->productModel->update($id, [
            'categoriaId' => $data['categoriaId'],
            'nome' => $data['nome'],
            'descricao' => $data['descricao'],
            'preco' => $data['preco'],
            'preco_comparativo' => $data['preco_comparativo'],
            'estoque' => $data['estoque'],
            'status' => $data['status'],
        ]);

        $imagesForm = $this->request->getFiles();

        if (isset($imagesForm['imagens'])) {
            $uploadedImages = $imagesForm['imagens'];
            foreach ($uploadedImages as $image) {
                if ($image->isValid()) {
                    $validImages[] = $image;
                }
            }
        }

        if (isset($validImages)) {
            $this->uploadImageGalery($validImages, $product->id);
        }

        if (isset($imagesForm['img'])) {
            if ($imagesForm['img']->isValid()) {
                $nameImg = $this->uploadImage($imagesForm['img']);
                $imagePath = FCPATH . $product->img;
                
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
                
                $this->productModel->update($product->id, [
                    'img' => $nameImg
                ]);
            }
        }

        if (!$updated) {
            return redirect()->route('admin.product.index')->withInput()->with('errors', $this->productModel->errors());
        }

        return redirect()->route('admin.product.index')->with('success', 'Produto atualizada com sucesso');
    }

    public function destroy($id)
    {
        $product = $this->productModel->find($id);

        if (!$product) {
            return redirect()->route('admin.product.index')->with('error', 'Ocorreu um erro.');
        }

        $this->productModel->delete($id);

        return redirect()->route('admin.product.index')->with('success', 'Produto excluído com sucesso');
    }

    public function xlsx()
    {
        $apiUrl = 'https://jsonforexcel.azurewebsites.net/api/Central/Json/Excel';

        $query = $this->filter_admin();

        $jsonData = $query->findAll();

        $dataProdutos = array_map(function ($dataProduct) {
            $categoria = $this->categoryModel->find($dataProduct->categoriaId);
            $status = $dataProduct->status === '1' ? 'Ativo' : 'Inativo';
            return [
                'Nome' => $dataProduct->nome,
                'Slug' => $dataProduct->slug,
                'Descrição' => $dataProduct->descricao,
                'Preço' => 'R$ ' . mask_valor($dataProduct->preco),
                'Preço Comparativo' => $dataProduct->preco_comparativo ? 'R$ ' . mask_valor($dataProduct->preco_comparativo) : 'N/A',
                'Estoque' => $dataProduct->estoque,
                'Status' => $status,
                'Categoria' => $categoria->nome,
                'Criado em' => date('d/m/Y', strtotime($dataProduct->created_at)),
                'Última atualização' => date('d/m/Y', strtotime($dataProduct->updated_at)),
                'Excluído em' => $dataProduct->deleted_at === null ? 'N/Excluido' : date('d/m/Y', strtotime($dataProduct->deleted_at))
            ];
        }, $jsonData);

        $produtos = json_encode($dataProdutos);

        $requestData = [
            'json' => $produtos,
            'sheetName' => 'ProdutosList',
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
                ->setHeader('Content-Disposition', 'attachment; filename="list_produtos.xlsx"')
                ->setBody($fileContents);
        } else {
            return redirect()->back()->with('error', 'Erro ao extrair planilha');
        }
    }

    public function deleteImg($imgId)
    {
        $img = $this->imagensProdutoModel->find($imgId);
        
        if($img){
            $imagePath = FCPATH . $img->caminho;
            if (file_exists($imagePath)) {
                unlink($imagePath);
                $this->imagensProdutoModel->where('id', $img->id)->delete();
            }
        }
    }

    private function filter_admin()
    {
        $search = $this->request->getVar('search');
        $status = $this->request->getVar('status');
        $categoria = $this->request->getVar('categoria');

        $query = $this->productModel->select('produtos.*, categorias.nome as categoria_nome')
            ->join('categorias', 'categorias.id = produtos.categoriaId')
            ->orderBy('produtos.status', 'DESC')
            ->orderBy('produtos.id', 'DESC');

        if ($search !== null && $search !== '') {
            $query->like('produtos.nome', $search);
        }

        if ($status !== null && $status !== '') {
            $query->where('produtos.status', $status);
        }

        if ($categoria !== null && $categoria !== '') {
            $query->where('produtos.categoriaId', $categoria);
        }

        return $query;
    }

    private function filter()
    {
        $data = $this->request;
        
        $search = $this->request->getVar('search');
        $categoria = $this->request->getVar('categoria');
        $max_preco = str_replace('.', '', $this->request->getVar('max_preco'));
        $max_preco = str_replace(',', '.', $max_preco);
        $min_preco = str_replace('.', '', $this->request->getVar('min_preco'));
        $min_preco = str_replace(',', '.', $min_preco);
        $order = $this->request->getVar('order');

        $query = $this->productModel->select('produtos.*, categorias.nome as categoria_nome')
            ->join('categorias', 'categorias.id = produtos.categoriaId')
            ->where('produtos.status', 1)
            ->where('categorias.status', 1)
            ->where('produtos.estoque >=', 1);

        if (!empty($categoria)) {
            $query->where('produtos.categoriaId', $categoria);
        }
    
        if (!empty($search)) {
            $query->like('produtos.nome', $search);
        }
    
        if (!empty($max_preco) && $max_preco > 0.00) {
            $query->where('produtos.preco <=', $max_preco);
        }
    
        if (!empty($min_preco) && $min_preco > 0.00) {
            $query->where('produtos.preco >=', $min_preco);
        }
    
        if (!empty($order) && in_array(strtolower($order), ['asc', 'desc'])) {
            $query->orderBy('produtos.preco', $order);
        } else {
            $query->orderBy('produtos.created_at', 'DESC');
        }

        return $query;
    }

    private function uploadImage($img)
    {
        $img_name = 'assets/product_images/' . date('mdy') . $img->getRandomName();

        \Config\Services::image('gd')
            ->withFile($img)
            ->resize(500, 500, true)
            ->save(FCPATH . $img_name);

        return $img_name;
    }

    private function uploadImageGalery(array $imgs, int $produtoId)
    {
        foreach ($imgs as $img) {
            $img_name = 'assets/product_images/' . date('mdy') . $img->getRandomName();

            \Config\Services::image('gd')
                ->withFile($img)
                ->resize(500, 500, true)
                ->save(FCPATH . $img_name);

            $this->imagensProdutoModel->insert([
                'caminho' => $img_name,
                'produtoId' => $produtoId
            ]);
        }
    }

    private function getImagesByProductId($productId)
    {
        return $this->imagensProdutoModel
                        ->where('produtoId', $productId)
                        ->findAll();
    }
}
