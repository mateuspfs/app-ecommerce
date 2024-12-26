<?= $this->extend('admin/master') ?>

<?= $this->section('title') ?>
    Listagem Categorias
<?= $this->endSection() ?>

<?= $this->section('content') ?>

    <div class="container">

        <div class="d-flex justify-content-end mb-4 align-items-center">

            <a href="<?= url_to('category.xlsx') ?>?search=<?= isset($_GET['search']) ? $_GET['search'] : '' ?>&status=<?= isset($_GET['status']) ? $_GET['status'] : '' ?>" class="ms-1"><img src="<?= base_url('theme_admin/docs/assets/img/xlsx.png') ?>" alt="XLSX" style="width: 50px;"></a>

            <button type="button" class="btn btn-primary m-4" data-toggle="modal" data-target="#AddCategoria">
                Adicionar Categoria
            </button>
        </div>

        <form action="<?= url_to('category.index') ?>" method="GET">
            <div class="input-group mb-4">
                <input type="text" class="form-control-lg border border-dark" placeholder="Procure por nome" name="search" value="<?= isset($_GET['search']) ? $_GET['search'] : '' ?>">
                
                <select name="status" class="form-control-lg ml-2 mr-2 border border-dark"> 
                    <option value="" hidden selected>Status</option>
                    <option value="1" <?= isset($_GET['status']) && $_GET['status'] === '1' ? 'selected' : '' ?>>Ativo</option>
                    <option value="0" <?= isset($_GET['status']) && $_GET['status'] === '0' ? 'selected' : '' ?>>Inativo</option>
                </select>

                <button type="submit" class="btn btn-outline-secondary mx-2 border border-dark bg-white" id="button-addon2"><img src="<?= base_url('theme_admin/docs/assets/img/lupa.png') ?>" width="20px" alt="Lupa"></button>
                <a href="<?= url_to('category.index') ?>" class="btn btn-outline-secondary border border-dark bg-white" id="button-addon2"><img src="<?= base_url('theme_admin/docs/assets/img/reset.png') ?>" width="20px" alt="Lupa" class="mt-2"></a>
            </div>
            <div class="input-group mb-4 border border-black">
                
            </div>
        </form>

        <div class="row">
            <div class="col-md-12">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php  foreach($categories as $category): ?>
                        <tr>
                            <td><?= $category->nome ?></td>
                            <td><?= $category->status === '1' ? 'Ativo' : 'Inativo'; ?></td>
                            <td>

                                <a data-toggle="modal" data-target="#EditCategoria_<?= $category->id ?>" class="btn btn-primary btn-sm" ><img src="<?= base_url('theme_admin/docs/assets/img/edit.png') ?>" alt="" width="20px"/></a>

                                <!-- Modal de edição de dados-->
                                <div class="modal fade" id="EditCategoria_<?= $category->id ?>" tabindex="-1" aria-labelledby="EditcategoryLabel<?= $category->id ?>" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">

                                            <div class="modal-header">
                                                <h5 class="modal-title">Editar dados do Categoria</h5>
                                                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                                            </div>

                                            <div class="modal-body">

                                                <form action="<?= url_to('category.update', $category->id) ?>" method="post">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="_method" value="PUT">
                                                    <input type="hidden" name="id" value="<?= $category->id ?>" required>

                                                    <div class="form-group mb-4">     
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">Nome</span>
                                                            </div>
                                                            <input type="text" class="form-control" name="nome" value="<?= $category->nome ?>" required>
                                                        </div>
                                                    </div>

                                                    <div class="form-group mb-4">     
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">Status</span>
                                                            </div>
                                                            <select name="status" class="form-control">
                                                                <option value="1" <?php if($category->status == 1) echo 'selected'; ?>>Ativo</option>
                                                                <option value="0" <?php if($category->status == 0) echo 'selected'; ?>>Inativo</option>
                                                            </select>
                                                        </div>
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

                                <a data-toggle="modal" data-target="#deleteCategoria_<?= $category->id ?>" class="btn btn-danger btn-sm" ><img src="<?= base_url('theme_admin/docs/assets/img/lixo.png') ?>" alt="" width="20px"/></a>

                                <!-- Modal de confirmação de exclusão -->
                                <div class="modal fade" id="deleteCategoria_<?= $category->id ?>" tabindex="-1" aria-labelledby="deleteModalLabel_<?= $category->id ?>" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                        <div class="modal-content p-4">
                                            <div class="modal-body text-center">
                                                <h2 class="display-6">Tem certeza que deseja excluir essa categoria?</h2>
                                                <div class="mt-5 d-flex text-center justify-content-center align-items-center">
                                                    <button class="btn btn-lg btn-danger m-4" data-dismiss="modal">Não</button>
                                                    <form action="<?= url_to('category.destroy', $category->id) ?>" method="POST">
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
                        <?php  endforeach; ?>
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
                    <?php if ($pagination->getCurrentPage() > 4): ?>
                        <li class="page-item disabled">
                            <span class="page-link bg-dark text-white">...</span>
                        </li>
                    <?php endif; ?>

                    <?php for ($page = max(2, $pagination->getCurrentPage() - 2); $page <= min($pagination->getPageCount() - 1, $pagination->getCurrentPage() + 2); $page++): ?>
                        <li class="page-item <?= $page == $pagination->getCurrentPage() ? 'active' : '' ?>">
                            <a class="page-link bg-dark text-white" href="<?= $pagination->getPageURI($page) ?>"><?= $page ?></a>
                        </li>
                    <?php endfor; ?>

                    <!-- Link para a última página -->
                    <?php if ($pagination->getCurrentPage() <  $pagination->getPageCount() - 3): ?>
                        <li class="page-item disabled">
                            <span class="page-link bg-dark text-white">...</span>
                        </li>
                    <?php endif; ?>
                    
                    <?php if($pagination->getPageCount() > 1):?>
                        <li class="page-item <?= $pagination->getCurrentPage() ==  $pagination->getPageCount() ? 'active' : '' ?>">
                            <a class="page-link bg-dark text-white" href="<?= $pagination->getPageURI($pagination->getPageCount()) ?>"><?=  $pagination->getPageCount() ?></a>
                        </li>
                    <?php endif ?>
                    
                    <!-- Link para a próxima página -->
                    <li class="page-item <?= $pagination->getCurrentPage() ==  $pagination->getPageCount() ? 'disabled' : '' ?>">
                        <a class="page-link bg-dark text-white" href="<?= $pagination->getNextPageURI() ?>">Próxima</a>
                    </li>
                </ul>
            </nav>
        </div>
        <p class="d text-center text-muted">Página <?= $pagination->getCurrentPage() ?> de <?=  $pagination->getPageCount() ?>.</p>
    </div>

    <!-- Modal para adicionar Categorias-->
    <div class="modal fade" id="AddCategoria" tabindex="-1" aria-labelledby="AddCategoriaLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Adicionar Categoria</h5>
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <form action="<?= url_to('category.store') ?>" method="post">
                        <?=  csrf_field() ?>
                        <div class="form-group mb-4">     
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Nome</span>
                                </div>
                                <input type="text" class="form-control" id="nome" name="nome" value="<?= old('nome') ?>" required>
                            </div>
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

<?= $this->endSection() ?>