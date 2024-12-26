<?= $this->extend('admin/master') ?>

<?= $this->section('title') ?>
Listagem Produtos
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="container">
    <style>
        .image-gallery {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .image-gallery .image-container {
            position: relative;
            width: 23%;
        }

        .image-gallery img {
            width: 100%;
            height: auto;
            border-radius: 5px;
        }

        .image-gallery .delete-btn {
            position: absolute;
            top: 5px;
            right: 5px;
            background: red;
            color: white;
            border: none;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            text-align: center;
            line-height: 20px;
            cursor: pointer;
        }
    </style>

    <div class="d-flex justify-content-end mb-4 align-items-center">

        <a href="<?= url_to('product.xlsx') ?>?search=<?= isset($_GET['search']) ? $_GET['search'] : '' ?>&status=<?= isset($_GET['status']) ? $_GET['status'] : '' ?>&tipo=<?= isset($_GET['tipo']) ? $_GET['tipo'] : '' ?>" class="ms-1"><img src="<?= base_url('theme_admin/docs/assets/img/xlsx.png') ?>" alt="XLSX" style="width: 50px;"></a>

        <button type="button" class="btn btn-primary m-4" data-toggle="modal" data-target="#AddProduto">
            Adicionar Produto
        </button>
    </div>

    <form action="<?= url_to('admin.product.index') ?>" method="GET">
        <div class="input-group mb-4">
            <input type="text" class="form-control-lg border border-dark" placeholder="Procure por nome ou código" name="search" value="<?= isset($_GET['search']) ? $_GET['search'] : '' ?>">

            <select name="status" class="form-control-lg ml-2 mr-2 border border-dark">
                <option value="" hidden selected>Status</option>
                <option value="1" <?= isset($_GET['status']) && $_GET['status'] === '1' ? 'selected' : '' ?>>Ativo</option>
                <option value="0" <?= isset($_GET['status']) && $_GET['status'] === '0' ? 'selected' : '' ?>>Inativo</option>
            </select>

            <select name="categoria" class="form-control-lg ml-2 mr-2 border border-dark">
                <option value="" hidden selected>Selecione</option>
                <?php foreach ($categorias as $categoria) : ?>
                    <option value="<?= $categoria->id ?>" <?= isset($_GET['categoria']) && $_GET['categoria'] === $categoria->id ? 'selected' : '' ?>><?= $categoria->nome ?></option>
                <?php endforeach ?>
            </select>

            <button type="submit" class="btn btn-outline-secondary mx-2 bg-white border border-dark" id="button-addon2"><img src="<?= base_url('theme_admin/docs/assets/img/lupa.png') ?>" width="20px" alt="Lupa"></button>
            <a href="<?= url_to('admin.product.index') ?>" class="btn btn-outline-secondary bg-white border border-dark" id="button-addon2"><img src="<?= base_url('theme_admin/docs/assets/img/reset.png') ?>" width="20px" alt="Lupa" class="mt-2"></a>
        </div>
    </form>

    <div class="row">
        <div class="col-md-12">
            <table class="table">
                <thead>
                    <tr class="text-center">
                        <th>Imagem</th>
                        <th>Nome</th>
                        <th>Categoria</th>
                        <th style="width: 150px;">Preço Comparativo</th>
                        <th style="width: 150px;">Preço</th>
                        <th>Estoque</th>
                        <th>Status</th>
                        <th style="width: 150px;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($produtos as $produto) : ?>
                        <tr class="text-center">
                            <td><img src="<?= asset($produto->img) ?>" alt="" width="100px" height="100px"></td>
                            <td><?= mask_title($produto->nome) ?></td>
                            <td><?= esc($produto->categoria_nome) ?></td>
                            <td><?= ($produto->preco_comparativo != null) ? 'R$ ' . esc(mask_valor($produto->preco_comparativo)) : 'N/informado' ?></td>
                            <td><?= 'R$ ' . esc(mask_valor($produto->preco)) ?></td>
                            <td><?= esc($produto->estoque) ?></td>
                            <td><?= esc($produto->status) === '1' ? 'Ativo' : 'Inativo'; ?></td>
                            <td>

                                <a data-toggle="modal" data-target="#EditProduto_<?= esc($produto->id) ?>" class="btn btn-primary btn-sm"><img src="<?= base_url('theme_admin/docs/assets/img/edit.png') ?>" alt="" width="20px" /></a>

                                <!-- Modal de edição de dados-->
                                <div class="modal fade" id="EditProduto_<?= esc($produto->id) ?>" tabindex="-1" aria-labelledby="EditProdutoLabel<?= esc($produto->id) ?>" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Editar Produto</h5>
                                                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form action="<?= url_to('product.update', esc($produto->id)) ?>" method="post" enctype="multipart/form-data">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="_method" value="PUT">
                                                    <input type="hidden" name="id" value="<?= esc($produto->id) ?>" required>

                                                    <div class="form-group mb-4">
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">Nome</span>
                                                            </div>
                                                            <input type="text" class="form-control" name="nome" value="<?= esc($produto->nome) ?>" required>
                                                        </div>
                                                    </div>

                                                    <div class="form-group mb-4">
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">Categoria</span>
                                                            </div>
                                                            <select name="categoriaId" class="form-control">
                                                                <option hidden selected>Selecione</option>
                                                                <?php foreach ($categorias as $categoria) : ?>
                                                                    <option value="<?= $categoria->id ?>" <?= $categoria->id === esc($produto->categoriaId) ? 'selected' : '' ?>><?= $categoria->nome ?></option>
                                                                <?php endforeach ?>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="input-group mb-4">
                                                        <span class="input-group-text">Preço Comparativo</span>
                                                        <span class="input-group-text">R$</span>
                                                        <input type="text" class="form-control input-money" name="preco_comparativo" value="<?= esc($produto->preco_comparativo) ?>">
                                                    </div>

                                                    <div class="input-group mb-4">
                                                        <span class="input-group-text">Preço</span>
                                                        <span class="input-group-text">R$</span>
                                                        <input type="text" class="form-control input-money" name="preco" value="<?= esc($produto->preco) ?>" required>
                                                    </div>

                                                    <div class="form-group mb-4">
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">Estoque</span>
                                                            </div>
                                                            <input type="text" class="form-control" name="estoque" value="<?= esc($produto->estoque) ?>" required>
                                                        </div>
                                                    </div>

                                                    <div class="input-group mb-4">
                                                        <span class="input-group-text">Descrição</span>
                                                        <textarea type="text" class="form-control" name="descricao"><?= esc($produto->descricao) ?></textarea>
                                                    </div>

                                                    <div class="form-group mb-4">
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">Status</span>
                                                            </div>
                                                            <select name="status" class="form-control">
                                                                <option value="1" <?php if (esc($produto->status) == 1) echo 'selected'; ?>>Ativo</option>
                                                                <option value="0" <?php if (esc($produto->status) == 0) echo 'selected'; ?>>Inativo</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="form-group mb-4">
                                                        <label for="imagem">Imagem Principal</label>
                                                        <br>
                                                        <img src="<?= asset($produto->img) ?>" alt="" width="60%">
                                                    </div>

                                                    <?php if (isset($produto->galeria) && !empty($produto->galeria)) : ?>
                                                        <div class="form-group mb-4">
                                                            <label for="imagem">Galeria</label>
                                                            <div class="image-gallery">
                                                                <?php foreach ($produto->galeria as $image) : ?>
                                                                    <div class="image-container">
                                                                        <img src="<?= asset($image->caminho) ?>" alt="">
                                                                        <button type="button" class="delete-btn" data-id="<?= $image->id ?>">X</button>
                                                                    </div>
                                                                <?php endforeach ?>
                                                            </div>
                                                        </div>
                                                    <?php endif ?>

                                                    <div class="form-group mb-4 mt-5">
                                                        <label for="imagem">Trocar imagem principal</label>
                                                        <input type="file" class="form-control mb-3 imagens-input" name="img" accept="image/*">
                                                        <div class="image-preview main-image-preview mt-3 row"></div>
                                                    </div>

                                                    <div class="form-group mb-4">
                                                        <label for="imagem">Adicionar mais imagens a galeria</label>
                                                        <input type="file" class="form-control imagens-input" name="imagens[]" accept="image/*" multiple>
                                                        <div class="image-preview gallery-image-preview mt-3 row"></div>
                                                        <input type="hidden" class="selected-images" name="selected_images">
                                                    </div>

                                                    <div class="d-flex justify-content-center">
                                                        <button type="button" class="btn btn-secondary mt-4 mx-3" data-dismiss="modal">Cancelar</button>
                                                        <button type="submit" class="btn btn-primary mt-4 float-left">Salvar</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <a data-toggle="modal" data-target="#deleteProduto_<?= esc($produto->id) ?>" class="btn btn-danger btn-sm"><img src="<?= base_url('theme_admin/docs/assets/img/lixo.png') ?>" alt="" width="20px" /></a>

                                <!-- Modal de confirmação de exclusão -->
                                <div class="modal fade" id="deleteProduto_<?= esc($produto->id) ?>" tabindex="-1" aria-labelledby="deleteModalLabel_<?= esc($produto->id) ?>" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                        <div class="modal-content p-4">
                                            <div class="modal-body text-center">
                                                <h2 class="display-6">Tem certeza que deseja excluir esse produto?</h2>
                                                <div class="mt-5 d-flex text-center justify-content-center align-items-center">
                                                    <button class="btn btn-lg btn-danger m-4" data-dismiss="modal">Não</button>
                                                    <form action="<?= url_to('product.destroy', esc($produto->id)) ?>" method="POST">
                                                        <?= csrf_field() ?>
                                                        <input type="hidden" name="_method" value="DELETE">
                                                        <button type="submit" class="btn btn-lg btn-success">Sim</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <nav class="container d-flex justify-content-center">
            <ul class="pagination m-4">
                <!-- Link para a página anterior -->
                <li class="page-item <?= $pagination->getCurrentPage() == 1 ? 'disabled' : '' ?>">
                    <a class="page-link bg-dark text-white" href="<?= $pagination->getPreviousPageURI() ?>">Anterior</a>
                </li>

                <!-- Link para a primeira página -->
                <li class="page-item <?= $pagination->getCurrentPage() == 1 ? 'active' : '' ?>">
                    <a class="page-link bg-dark text-white" href="<?= $pagination->getPageURI(1) ?>">1</a>
                </li>

                <!-- Links intermediários (somente se necessário) -->
                <?php if ($pagination->getCurrentPage() > 4) : ?>
                    <li class="page-item disabled">
                        <span class="page-link bg-dark text-white">...</span>
                    </li>
                <?php endif; ?>

                <?php for ($page = max(2, $pagination->getCurrentPage() - 2); $page <= min($pagination->getPageCount() - 1, $pagination->getCurrentPage() + 2); $page++) : ?>
                    <li class="page-item <?= $page == $pagination->getCurrentPage() ? 'active' : '' ?>">
                        <a class="page-link bg-dark text-white" href="<?= $pagination->getPageURI($page) ?>"><?= $page ?></a>
                    </li>
                <?php endfor; ?>

                <!-- Link para a última página -->
                <?php if ($pagination->getCurrentPage() <  $pagination->getPageCount() - 3) : ?>
                    <li class="page-item disabled">
                        <span class="page-link bg-dark text-white">...</span>
                    </li>
                <?php endif; ?>

                <?php if ($pagination->getPageCount() > 1) : ?>
                    <li class="page-item <?= $pagination->getCurrentPage() ==  $pagination->getPageCount() ? 'active' : '' ?>">
                        <a class="page-link bg-dark text-white" href="<?= $pagination->getPageURI($pagination->getPageCount()) ?>"><?= $pagination->getPageCount() ?></a>
                    </li>
                <?php endif ?>

                <!-- Link para a próxima página -->
                <li class="page-item <?= $pagination->getCurrentPage() ==  $pagination->getPageCount() ? 'disabled' : '' ?>">
                    <a class="page-link bg-dark text-white" href="<?= $pagination->getNextPageURI() ?>">Próxima</a>
                </li>
            </ul>
        </nav>
    </div>
    <p class="d text-center text-muted">Página <?= $pagination->getCurrentPage() ?> de <?= $pagination->getPageCount() ?>.</p>
</div>

<!-- Modal para adicionar Produtos-->
<div class="modal fade" id="AddProduto" tabindex="-1" aria-labelledby="AddProdutoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Adicionar Produto</h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="<?= url_to('product.store') ?>" method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>

                    <div class="form-group mb-4">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Nome</span>
                            </div>
                            <input type="text" class="form-control" name="nome" value="<?= esc(old('nome')) ?>" required>
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Categoria</span>
                            </div>
                            <select name="categoriaId" class="form-control">
                                <option hidden selected>Selecione</option>
                                <?php foreach ($categorias as $categoria) : ?>
                                    <option value="<?= $categoria->id ?>" <?= $categoria->id === esc(old('categoriaId')) ? 'selected' : '' ?>><?= $categoria->nome ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                    </div>

                    <div class="input-group mb-4">
                        <span class="input-group-text">Preço Comparativo</span>
                        <span class="input-group-text">R$</span>
                        <input type="text" class="form-control input-money" name="preco_comparativo" value="<?= esc(old('preco_comparativo')) ?>">
                    </div>

                    <div class="input-group mb-4">
                        <span class="input-group-text">Preço</span>
                        <span class="input-group-text">R$</span>
                        <input type="text" class="form-control input-money" name="preco" value="<?= esc(old('preco')) ?>" required>
                    </div>

                    <div class="form-group mb-4">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Estoque</span>
                            </div>
                            <input type="text" class="form-control" name="estoque" value="<?= esc(old('estoque')) ?>" required>
                        </div>
                    </div>

                    <div class="input-group mb-4">
                        <span class="input-group-text">Descrição</span>
                        <textarea type="text" class="form-control" name="descricao"><?= esc(old('descricao')) ?></textarea>
                    </div>

                    <div class="form-group mb-4">
                        <label for="imagem">Imagem principal</label>
                        <input type="file" class="form-control mb-3 imagens-input" name="img" accept="image/*">
                        <div class="image-preview main-image-preview mt-3 row"></div>
                    </div>

                    <div class="form-group mb-4">
                        <label for="imagem">Galeria de imagens do produto</label>
                        <input type="file" class="form-control imagens-input" name="imagens[]" accept="image/*" multiple>
                        <div class="image-preview gallery-image-preview mt-3 row"></div>
                        <input type="hidden" class="selected-images" name="selected_images">
                    </div>

                    <div class="d-flex justify-content-center">
                        <button type="button" class="btn btn-secondary mt-4 mx-3" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary mt-4 float-left">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script>
    var apiDeleteImg = '<?= url_to('product.delete.img', '') ?>';
</script>
<script src="<?= base_url('assets/js/produtos-admin.js') ?>"></script>
<?= $this->endSection() ?>