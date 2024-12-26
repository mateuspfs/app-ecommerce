<?= $this->extend('admin/master') ?>

<?= $this->section('title') ?>
    Listagem Pedidos
<?= $this->endSection() ?>

<?= $this->section('content') ?>

    <div class="container">

        <div class="d-flex justify-content-end mb-4 align-items-center">

            <a href="<?= url_to('order.xlsx') ?>?search=<?=isset($_GET['search'])?$_GET['search']:''?>&status=<?=isset($_GET['status'])?$_GET['status']:''?>&tipo=<?=isset($_GET['tipo'])?$_GET['tipo']:''?>" class="ms-1 mt-4 me-2"><img src="<?= base_url('theme_admin/docs/assets/img/xlsx.png') ?>" alt="XLSX" style="width: 50px;"></a>

        </div>

        <form action="<?= url_to('admin.order.index') ?>" method="GET">
            <div class="input-group mb-4">
                <input type="text" class="form-control-lg border border-dark" placeholder="Procure por nome ou código" name="search" value="<?= isset($_GET['search']) ? $_GET['search'] : '' ?>">
                
                <select name="status" class="form-control-lg ml-2 mr-2 border border-dark"> 
                    <option value="" hidden selected>Status</option>
                    <option value="Aguardando Pagamento" <?= isset($_GET['status']) && $_GET['status'] === 'Aguardando Pagamento' ? 'selected' : '' ?>>Aguardando Pagamento</option>
                    <option value="Cancelado" <?= isset($_GET['status']) && $_GET['status'] === 'Cancelado' ? 'selected' : '' ?>>Cancelado</option>
                    <option value="Pago" <?= isset($_GET['status']) && $_GET['status'] === 'Pago' ? 'selected' : '' ?>>Pago</option>
                    <option value="Em entrega" <?= isset($_GET['status']) && $_GET['status'] === 'Em entrega' ? 'selected' : '' ?>>Em entrega</option>
                    <option value="Concluído" <?= isset($_GET['status']) && $_GET['status'] === 'Concluído' ? 'selected' : '' ?>>Concluído</option>
                </select>

                <button type="submit" class="btn btn-outline-secondary mx-2 bg-white border border-dark" id="button-addon2"><img src="<?= base_url('theme_admin/docs/assets/img/lupa.png') ?>" width="20px" alt="Lupa"></button>
                <a href="<?= url_to('admin.order.index') ?>" class="btn btn-outline-secondary bg-white border border-dark" id="button-addon2"><img src="<?= base_url('theme_admin/docs/assets/img/reset.png') ?>" width="20px" alt="Lupa" class="mt-2"></a>
            </div>
        </form>

        <div class="row">
            <div class="col-md-12">
                <table class="table">
                    <thead>
                        <tr class="text-center">
                            <th>Código</th>
                            <th>Usuario</th>
                            <th style="width: 150px;">Valor</th>
                            <th>Cupom</th>
                            <th>Status</th>
                            <th>Feito em</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php  foreach($orders as $order): ?>
                        <tr class="text-center">
                            <td>#<?= esc($order->codigo) ?></td>
                            <td><?= esc($order->nm_user) ?></td>
                            <td><?= 'R$ ' . mask_valor($order->valor) ?></td>
                            <td><?= ($order->cupomId) ? esc($order->cupom) : 'N/Utilizado' ?></td>
                            <td> <?= esc($order->status) ?></td>
                            <td><?= esc(date('d/m/Y', strtotime($order->created_at))) ?></td>
                            <td>
                                <a data-codigo="<?= esc($order->codigo) ?>" class="btn btn-primary btn-sm view-order-details"><img src="<?= base_url('template_site/images/info.png') ?>" alt="" width="20px"/></a>

                                <a data-toggle="modal" data-target="#deleteProduto_<?= esc($order->id) ?>" class="btn btn-danger btn-sm"><img src="<?= base_url('theme_admin/docs/assets/img/lixo.png') ?>" alt="" width="20px"/></a>

                                <!-- Modal de confirmação de exclusão -->
                                <div class="modal fade" id="deleteProduto_<?= esc($order->id) ?>" tabindex="-1" aria-labelledby="deleteModalLabel_<?= esc($order->id) ?>" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                        <div class="modal-content p-4">
                                            <div class="modal-body text-center">
                                                <h2 class="display-6">Tem certeza que deseja excluir esse pedido?</h2>
                                                <div class="mt-5 d-flex text-center justify-content-center align-items-center">
                                                    <button class="btn btn-lg btn-danger m-4" data-dismiss="modal">Não</button>
                                                    <form action="<?= url_to('order.destroy', esc($order->id)) ?>" method="POST">
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

    <!-- Modal de detalhes do pedido -->
    <div class="modal fade" id="orderDetailsModal" tabindex="-1" aria-labelledby="orderDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="orderDetailsModalLabel">Detalhes do Pedido</h5>
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="orderDetailsContent">
                    <!-- Os detalhes do pedido são carregados aqui via AJAX -->
                </div>
            </div>
        </div>
    </div>

<?= $this->endSection() ?>

<?= $this->section('script') ?>
    <script> 
        $(document).ready(function() {
            $('.view-order-details').on('click', function() {
                const orderCode = $(this).data('codigo');
                $.ajax({
                    url: '<?= url_to('admin.integra.order', '') ?>' + orderCode,
                    method: 'GET',
                    success: function(response) {
                        $('#orderDetailsContent').html(response);
                        $('#orderDetailsModal').modal('show');
                    }
                });
            });
        });
    </script>
<?= $this->endSection() ?>