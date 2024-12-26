<?= $this->extend('admin/master') ?>

<?= $this->section('title') ?>
    Listagem Usuários
<?= $this->endSection() ?>

<?= $this->section('content') ?>

    <div class="container">

        <div class="d-flex justify-content-end mb-4 align-items-center">

            <a href="<?= url_to('user.xlsx') ?>?search=<?=isset($_GET['search'])?$_GET['search']:''?>&status=<?=isset($_GET['status'])?$_GET['status']:''?>&cpf=<?=isset($_GET['cpf'])?$_GET['cpf']:''?>" class="ms-1"><img src="<?= base_url('theme_admin/docs/assets/img/xlsx.png') ?>" alt="XLSX" style="width: 50px;"></a>

            <button type="button" class="btn btn-primary m-4" data-toggle="modal" data-target="#AddAdmin">
                Adicionar Usuário
            </button>
        </div>

        <form action="<?= url_to('user.index') ?>" method="GET">
            <div class="input-group mb-4">
                <input type="text" class="form-control-lg border border-dark" placeholder="Busque por nome ou email" name="search" value="<?= isset($_GET['search']) ? $_GET['search'] : '' ?>">
                <input type="text" class="form-control-lg mx-2 border border-dark" placeholder="Busque por CPF" name="cpf" value="<?= isset($_GET['cpf']) ? $_GET['cpf'] : '' ?>">
                
                <select name="status" class="form-control-lg mr-2 border border-dark"> 
                    <option hidden selected value="">Status</option>
                    <option value="1" <?= isset($_GET['status']) && $_GET['status'] === '1' ? 'selected' : '' ?>>Ativo</option>
                    <option value="0" <?= isset($_GET['status']) && $_GET['status'] === '0' ? 'selected' : '' ?>>Inativo</option>
                </select>

                <button type="submit" class="btn btn-outline-secondary mx-2 border border-dark bg-white" id="button-addon2"><img src="<?= base_url('theme_admin/docs/assets/img/lupa.png') ?>" width="20px" alt="Lupa"></button>
                <a href="<?= url_to('user.index') ?>" class="btn btn-outline-secondary border border-dark bg-white" id="button-addon2"><img src="<?= base_url('theme_admin/docs/assets/img/reset.png') ?>" width="20px" alt="Lupa" class="mt-2"></a>
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
                            <th>CPF</th>
                            <th>Telefone</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php  foreach($users as $user): ?>
                        <tr>
                            <td><?= $user->nome ?></td>
                            <td><?= mask_cpf( $user->cpf) ?></td>
                            <td><?= mask_telefone($user->telefone) ?></td>
                            <td><?= $user->email ?></td>
                            <td><?= $user->status === '1' ? 'Ativo' : 'Inativo'; ?></td>
                            <td>

                                <a data-toggle="modal" data-target="#EditAdmin_<?= $user->id ?>" class="btn btn-primary btn-sm" ><img src="<?= base_url('theme_admin/docs/assets/img/edit.png') ?>" alt="" width="20px"/></a>

                                <!-- Modal de edição de dados-->
                                <div class="modal fade" id="EditAdmin_<?= $user->id ?>" tabindex="-1" aria-labelledby="EdituserLabel<?= $user->id ?>" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">

                                            <div class="modal-header">
                                                <h5 class="modal-title">Editar dados do Usuário</h5>
                                                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                                            </div>

                                            <div class="modal-body">

                                                <form action="<?= url_to('admin.user.update', $user->id) ?>" method="post" x-data>
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="_method" value="PUT">
                                                    <input type="hidden" name="id" value="<?= $user->id ?>" required>


                                                    <div class="form-group mb-4">     
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">Nome</span>
                                                            </div>                                 
                                                            <input type="text" class="form-control" name="nome" value="<?= $user->nome ?>" required>
                                                        </div>
                                                    </div>
                                                    
                                                     <div class="form-group mb-4">     
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">CPF</span>
                                                            </div>                                                                                           
                                                            <input type="text" x-mask="999.999.999-99" class="form-control" name="cpf" value="<?= $user->cpf ?>" required>
                                                        </div>
                                                    </div>

                                                     <div class="form-group mb-4">     
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">Contato</span>
                                                            </div>                                                                                   
                                                            <input type="text" x-mask="(99) 99999-9999" class="form-control" name="telefone" value="<?= $user->telefone ?>" required>                                        
                                                        </div>
                                                     </div>

                                                    <div class="form-group mb-4">     
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">Email</span>
                                                            </div>                                 
                                                            <input type="email" class="form-control" name="email" value="<?= $user->email ?>" required>
                                                        </div>
                                                    </div>

                                                    <div class="form-group mb-4">     
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">Status</span>
                                                            </div>                                                                                        
                                                            <select name="status" class="form-control">
                                                                <option value="1" <?php if($user->status == 1) echo 'selected'; ?>>Ativo</option>
                                                                <option value="0" <?php if($user->status == 0) echo 'selected'; ?>>Inativo</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="form-group mb-4">     
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">Nova senha</span>
                                                            </div>                                                                                       
                                                            <input type="password" class="form-control" name="password">
                                                        </div>
                                                    </div>

                                                    <div class="form-group mb-4">     
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">Confrme a senha</span>
                                                            </div>                                 
                                                            <input type="password" class="form-control" name="cpassword">
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

                                <a data-toggle="modal" data-target="#deleteAdmin_<?= $user->id ?>" class="btn btn-danger btn-sm" ><img src="<?= base_url('theme_admin/docs/assets/img/lixo.png') ?>" alt="" width="20px"/></a>

                                <!-- Modal de confirmação de exclusão -->
                                <div class="modal fade" id="deleteAdmin_<?= $user->id ?>" tabindex="-1" aria-labelledby="deleteModalLabel_<?= $user->id ?>" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                        <div class="modal-content p-4">
                                            <div class="modal-body text-center">
                                                <h2 class="display-6">Tem certeza que deseja excluir esse usuário?</h2>
                                                <div class="mt-5 d-flex text-center justify-content-center align-items-center">
                                                    <button class="btn btn-lg btn-danger m-4" data-dismiss="modal">Não</button>
                                                    <form action="<?= url_to('user.destroy', $user->id) ?>" method="POST">
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

    <!-- Modal para adicionar Admins-->
    <div class="modal fade" id="AddAdmin" tabindex="-1" aria-labelledby="AddAdminLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Adicionar Usuário</h5>
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <form action="<?= url_to('user.store') ?>" method="post" x-data>
                        <?=  csrf_field() ?>
                         <div class="form-group mb-4">     
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Nome</span>
                                </div>                                 
                                <input type="text" class="form-control" id="nome" name="nome" value="<?= old('nome') ?>" required>
                            </div>
                        </div>
                        
                         <div class="form-group mb-4">     
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">CPF</span>
                                </div>                                                           
                                <input type="text" x-mask="999.999.999-99" class="form-control" id="cpf" name="cpf" value="<?= old('cpf') ?>" required>
                            </div>
                        </div>

                         <div class="form-group mb-4">     
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Telefone</span>
                                </div>                                                            
                                <input type="text" x-mask="(99) 99999-9999" class="form-control" name="telefone" value="<?= old('telefone') ?>" required>
                            </div>
                        </div>

                         <div class="form-group mb-4">     
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Email</span>
                                </div>                                 
                                <input type="email" class="form-control" id="email" name="email" value="<?= old('email') ?>" required>
                            </div>
                        </div>
                        

                         <div class="form-group mb-4">     
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Senha</span>
                                </div>                                 
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                        </div>

                         <div class="form-group mb-4">     
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Confirme a senha</span>
                                </div>                                 
                                <input type="password" class="form-control" id="cpassword" name="cpassword" required>
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