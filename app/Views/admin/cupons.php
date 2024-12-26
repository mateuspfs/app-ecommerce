<?= $this->extend('admin/master') ?>

<?= $this->section('title') ?>
    Listagem Cupons
<?= $this->endSection() ?>

<?= $this->section('content') ?>

    <div class="container">

        <div class="d-flex justify-content-end mb-4 align-items-center">

            <a href="<?= url_to('cupom.xlsx') ?>?search=<?=isset($_GET['search'])?$_GET['search']:''?>&status=<?=isset($_GET['status'])?$_GET['status']:''?>&tipo=<?=isset($_GET['tipo'])?$_GET['tipo']:''?>" class="ms-1"><img src="<?= base_url('theme_admin/docs/assets/img/xlsx.png') ?>" alt="XLSX" style="width: 50px;"></a>

            <button type="button" class="btn btn-primary m-4" data-toggle="modal" data-target="#AddCupom">
                Adicionar Cupom
            </button>
        </div>

        <form action="<?= url_to('cupom.index') ?>" method="GET">
            <div class="input-group mb-4">
                <input type="text" class="form-control-lg border border-dark" placeholder="Procure por nome ou código" name="search" value="<?= isset($_GET['search']) ? $_GET['search'] : '' ?>">
                
                <select name="status" class="form-control-lg border border-dark ml-2 mr-2"> 
                    <option value="" hidden selected>Status</option>
                    <option value="1" <?= isset($_GET['status']) && $_GET['status'] === '1' ? 'selected' : '' ?>>Ativo</option>
                    <option value="0" <?= isset($_GET['status']) && $_GET['status'] === '0' ? 'selected' : '' ?>>Inativo</option>
                </select>

                <select name="tipo" class="form-control-lg border border-dark ml-2 mr-2"> 
                    <option value="" hidden selected>Tipo</option>
                    <option value="f" <?= isset($_GET['tipo']) && $_GET['tipo'] === 'f' ? 'selected' : '' ?>>Fixo</option>
                    <option value="p" <?= isset($_GET['tipo']) && $_GET['tipo'] === 'p' ? 'selected' : '' ?>>Porcentagem</option>
                </select>

                <button type="submit" class="btn btn-outline-secondary border border-dark mx-2 bg-white" id="button-addon2"><img src="<?= base_url('theme_admin/docs/assets/img/lupa.png') ?>" width="20px" alt="Lupa"></button>
                <a href="<?= url_to('cupom.index') ?>" class="btn btn-outline-secondary border border-dark bg-white" id="button-addon2"><img src="<?= base_url('theme_admin/docs/assets/img/reset.png') ?>" width="20px" alt="Lupa" class="mt-2"></a>
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
                            <th>Código</th>
                            <th>Tipo</th>
                            <th>P/cliente</th>
                            <th>Usado</th>
                            <th>Disponível</th>
                            <th>Desconto</th>
                            <th>Validade</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php  foreach($cupons as $cupom): ?>
                        <tr class="text-center">
                            <td><?= $cupom->nome ?></td>
                            <td><?= $cupom->codigo ?></td>
                            <td><?= $cupom->tipo === 'p' ? 'Porcentagem' : 'Fixo' ?></td>
                            <td><?= $cupom->qt_cliente ?></td>
                            <td><?= ($cupom->qt_usada === null) ? '0' : $cupom->qt_usada ?></td>
                            <td><?= $cupom->qt_disponivel ?></td>
                            <td><?= $cupom->tipo === 'p' ?  intval($cupom->desconto) . '%' :  'R$ ' . $cupom->desconto ?></td>
                                <?php
                                    $timestamp = new DateTime();
                                    $expired_at = new DateTime($cupom->expired_at);
                                    $currentDate = $timestamp->format('Y-m-d H:i:s');
                                    $cupomExpiredDate = $expired_at->format('Y-m-d H:i:s');  
                                ?>
                            <td style="
                                <?php  
                                    if(!($currentDate < $cupomExpiredDate)){ 
                                       echo "color:red";         
                                    }
                                ?>"
                            >
                                <?= date('d/m/Y', strtotime($cupom->expired_at)) ; ?>
                            </td>
                            <td><?= $cupom->status === '1' ? 'Ativo' : 'Inativo'; ?></td>
                            <td>

                                <a data-toggle="modal" data-target="#EditCupom_<?= $cupom->id ?>" class="btn btn-primary btn-sm" ><img src="<?= base_url('theme_admin/docs/assets/img/edit.png') ?>" alt="" width="20px"/></a>

                                <!-- Modal de edição de dados-->
                                <div class="modal fade" id="EditCupom_<?= $cupom->id ?>" tabindex="-1" aria-labelledby="EditcategoryLabel<?= $cupom->id ?>" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">

                                            <div class="modal-header">
                                                <h5 class="modal-title">Editar dados da Categoria</h5>
                                                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                                            </div>

                                            <div class="modal-body">

                                            <form action="<?= url_to('cupom.update', $cupom->id) ?>" method="post">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="_method" value="PUT">
                                                <input type="hidden" name="id" value="<?= $cupom->id ?>" required>

                                                <div class="form-group mb-4">     
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">Nome</span>
                                                        </div>
                                                        <input type="text" class="form-control" name="nome" value="<?= $cupom->nome ?>" required>
                                                    </div>
                                                </div>

                                                <div class="form-group mb-4">     
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">Código</span>
                                                        </div>
                                                        <input type="text" class="form-control" name="codigo" value="<?= $cupom->codigo ?>" required>
                                                    </div>
                                                </div>

                                                <div class="form-group mb-4">     
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">Tipo</span>
                                                        </div>
                                                        <select name="tipo" class="form-control">
                                                            <option value="f" <?= $cupom->tipo === 'f' ? 'selected' : '' ?>>Fixo</option>
                                                            <option value="p" <?= $cupom->tipo === 'p' ? 'selected' : '' ?>>Porcentagem</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="form-group mb-4">     
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">Desconto</span>
                                                        </div>
                                                        <input type="text" class="form-control" name="desconto" value="<?= $cupom->desconto ?>" required>
                                                    </div>
                                                </div>

                                                <div class="form-group mb-4">     
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">Quantidade</span>
                                                        </div>
                                                        <input type="text" class="form-control" name="qt_disponivel" value="<?= $cupom->qt_disponivel ?>" required>
                                                    </div>
                                                </div>

                                                <div class="form-group mb-4">     
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">Quantidade por cliente</span>
                                                        </div>
                                                        <input type="text" class="form-control" name="qt_cliente" value="<?= $cupom->qt_cliente ?>" required>
                                                    </div>
                                                </div>

                                                <div class="form-group mb-4">     
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">Data de validade</span>
                                                        </div>
                                                        <input type="date" class="form-control" name="expired_at" value="<?= date('Y-m-d', strtotime($cupom->expired_at)) ?>" min="<?= date('Y-m-d') ?>" required>
                                                    </div>
                                                </div>

                                                <div class="form-group mb-4">     
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">Status</span>
                                                        </div>
                                                        <select name="status" class="form-control">
                                                            <option value="1" <?= $cupom->status == 1 ? 'selected' : '' ?>>Ativo</option>
                                                            <option value="0" <?= $cupom->status == 0 ? 'selected' : '' ?>>Inativo</option>
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

                                <a data-toggle="modal" data-target="#deleteCupom_<?= $cupom->id ?>" class="btn btn-danger btn-sm" ><img src="<?= base_url('theme_admin/docs/assets/img/lixo.png') ?>" alt="" width="20px"/></a>

                                <!-- Modal de confirmação de exclusão -->
                                <div class="modal fade" id="deleteCupom_<?= $cupom->id ?>" tabindex="-1" aria-labelledby="deleteModalLabel_<?= $cupom->id ?>" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                        <div class="modal-content p-4">
                                            <div class="modal-body text-center">
                                                <h2 class="display-6">Tem certeza que deseja excluir esse cupom?</h2>
                                                <div class="mt-5 d-flex text-center justify-content-center align-items-center">
                                                    <button class="btn btn-lg btn-danger m-4" data-dismiss="modal">Não</button>
                                                    <form action="<?= url_to('cupom.destroy', $cupom->id) ?>" method="POST">
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

    <!-- Modal para adicionar Cupoms-->
    <div class="modal fade" id="AddCupom" tabindex="-1" aria-labelledby="AddCupomLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Adicionar Categoria</h5>
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="<?= url_to('cupom.store') ?>" method="post" x-data>
                        <?= csrf_field() ?>
                        
                        <div class="form-group mb-4">     
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Nome</span>
                                </div>
                                <input type="text" class="form-control" name="nome" value="<?= old('nome') ?>" required>
                            </div>
                        </div>

                        <div class="form-group mb-4">     
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Código</span>
                                </div>
                                <input type="text" x-mask="**********" class="form-control" name="codigo" value="<?= old('codigo') ?>" required>
                            </div>
                        </div>

                        <div class="form-group mb-4">     
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Tipo</span>
                                </div>
                                <select name="tipo" class="form-control"> 
                                    <option value="" hidden selected>Selecione</option>
                                    <option value="f" <?= (old('tipo') === 'f') ? 'selected' : '' ?>>Fixo</option>
                                    <option value="p" <?= (old('tipo') === 'p') ? 'selected' : '' ?>>Porcentagem</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group mb-4">     
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Desconto</span>
                                </div>
                                <input type="text" class="form-control" name="desconto" value="<?= old('desconto') ?>" required>
                            </div>
                        </div>

                        <div class="form-group mb-4">     
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Quantidade</span>
                                </div>
                                <input type="text" class="form-control" name="qt_disponivel" value="<?= old('qt_disponivel') ?>" required>
                            </div>
                        </div>

                        <div class="form-group mb-4">     
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Quantidade por cliente</span>
                                </div>
                                <input type="text" class="form-control" name="qt_cliente" value="<?= old('qt_cliente') ?>" required>
                            </div>
                        </div>

                        <div class="form-group mb-4">     
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Data de validade</span>
                                </div>
                                <input type="date" class="form-control" name="expired_at" value="<?= old('expired_at') ? old('expired_at') : '' ?>" min="<?= date('Y-m-d') ?>" required>
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