<!DOCTYPE HTML>
<html>

<head>
    <title>Pedidos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="<?= base_url('template_site/css/style.css') ?>" rel='stylesheet' type='text/css' />
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700,800' rel='stylesheet' type='text/css'>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>

<body>
    <div class="header">
        <div class="container">
            <div class="row">
                <div class="col-md-12 d-flex justify-content-between align-items-center">
                    <div class="header-left">
                        <div class="logo">
                            <a href="<?= url_to('index') ?>"><img src="<?= base_url('template_site/images/logo.png') ?>" alt="" /></a>
                        </div>
                    </div>
                    <div class="header-middle">
                        <!-- start search-->
                        <div class="mx-5">
                            <form action="<?= url_to('product.index') ?>" method="GET">
                                <div class="input-group w-500 ">
                                    <input class="form-control sb-search-input border border-dark" style="width: 500px;background-color: #333;color:white" type="text" name="search" id="search" value="<?= isset($_GET['search']) ? $_GET['search'] : '' ?>">
                                    <div class="input-group-append">
                                        <button class="btn sb-search-submit sb-icon-search" type="submit"></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="profile-box" style="margin-left: 40px;">
                            <a href="<?= url_to('cart.index') ?>" class="text-decoration-none">
                                <img src="<?= base_url('template_site/images/carrinho.png') ?>" alt="" width="30px" height="30px" />
                                <span class="cart-count" id="products-cart-count"></span>
                            </a>
                            <?php
                            if (!session()->has('user')) {
                                echo '<a class="btn btn-primary" style="margin-left: 20px;" href="' . url_to('user.create') . '">Cadastro</a>';
                                echo '<a class="btn btn-secondary bg-success" style="margin-left: 10px;" href="' . url_to('auth.login') . '">Login</a>';
                            } else { ?>
                                <div class="dropdown">
                                    <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                        <img src="<?= base_url('theme_admin/docs/assets/img/users.png') ?>" alt="User Icon" class="rounded-circle" width="30" height="30">
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenu2">
                                        <li><a class="dropdown-item" href="<?= url_to('user.edit') ?>" style="color: black">Meus Dados</a></li>
                                        <li><a class="dropdown-item" href="<?= url_to('order.index') ?>" style="color: black">Meus Pedidos</a></li>
                                        <li><a class="dropdown-item" href="<?= url_to('auth.logout') ?>" style="color: black">Sair</a></li>
                                    </ul>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            <hr class="bg-white">
            <div class="menu">
                <a class="toggleMenu" href="#"><img src="<?= base_url('template_site/images/nav.png') ?>" alt="" /></a>
                <ul class="nav" id="nav">
                    <li><a href="<?= url_to('index') ?>">Home</a></li>
                    <li><a href="<?= url_to('product.index') ?>">Produtos</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink">
                            Categorias
                        </a>
                        <div class="dropdown-menu drop-categories" aria-labelledby="navbarDropdownMenuLink" style="max-height: 400px; overflow-y: auto;">
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="main">
        <div class="shop_top">
            <div class="container d-flex justify-content-center">
                <div class="container">

                    <form action="<?= url_to('order.index') ?>" method="GET">
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
                            <a href="<?= url_to('order.index') ?>" class="btn btn-outline-secondary bg-white border border-dark" id="button-addon2"><img src="<?= base_url('theme_admin/docs/assets/img/reset.png') ?>" width="20px" alt="Lupa" class="mt-2"></a>
                        </div>
                    </form>

                    <div class="row">
                        <div class="col-md-12">
                            <table class="table">
                                <thead>
                                    <tr class="text-center">
                                        <th>Código</th>
                                        <th style="width: 150px;">Valor</th>
                                        <th>Cupom</th>
                                        <th>Status</th>
                                        <th>Feito em</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orders as $order) : ?>
                                        <tr class="text-center">
                                            <td>#<?= esc($order->codigo) ?></td>
                                            <td><?= 'R$ ' . mask_valor($order->valor) ?></td>
                                            <td><?= ($order->cupomId) ? esc($order->cupom) : 'N/Utilizado' ?></td>
                                            <td> <?= esc($order->status) ?></td>
                                            <td><?= esc(date('d/m/Y', strtotime($order->created_at))) ?></td>
                                            <td>
                                                <a data-codigo="<?= esc($order->codigo) ?>" class="btn btn-primary btn-sm view-order-details"><img src="<?= base_url('template_site/images/info.png') ?>" alt="" width="20px"/></a>

                                                <?php if (($order->status != 'Cancelado') && ($order->status != 'Concluído') && ($order->status != 'Em entrega')) : ?>

                                                    <a class="btn btn-danger btn-sm" data-toggle="modal" data-target="#cancelOrderModal_<?= esc($order->id) ?>"><img src="<?= base_url('template_site/images/cancelar.png') ?>" alt="" width="20px"/></a>

                                                    <!-- Modal de cancelamento pedido -->
                                                    <div class="modal fade" id="cancelOrderModal_<?= esc($order->id) ?>" tabindex="-1" aria-labelledby="cancelOrderModalLabel_<?= esc($order->id) ?>" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered modal-lg">
                                                            <div class="modal-content p-4">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="cancelOrderModalLabel_<?= esc($order->id) ?>">Cancelar Pedido #<?= esc($order->codigo) ?></h5>
                                                                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body text-center">
                                                                    <h2 class="display-6">Tem certeza que deseja cancelar esse pedido?</h2>
                                                                    <div class="mt-5 d-flex text-center justify-content-center align-items-center">
                                                                        <button class="btn btn-lg btn-secondary m-4" data-dismiss="modal">Não</button>
                                                                        <a href="<?= url_to('payment.cancel', $order->codigo) ?>" class="btn btn-lg btn-danger">Sim</a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endif ?>
                                            </td>
                                        </tr>
                                    <?php endforeach ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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


    <div class="footer">
        <div class="container text-center d-flex justify-content-center p-0">
            <div class="bg-dark">
                <div class="copy " style="background-color: #000;">
                    <p>© 2014 Template by <a href="http://w3layouts.com" target="_blank">w3layouts</a></p>
                    <p>Copyright &copy; 2024</p>
                </div>
            </div>
        </div>
    </div>

    <script src="<?= base_url('assets/js/master-public.js') ?>" defer></script>
    <!-- jQuery -->
    <script src="<?= base_url('theme_admin/plugins/jquery/jquery.min.js') ?>"></script>
    <!-- Bootstrap 4 -->
    <script src="<?= base_url('theme_admin/plugins/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
    <!-- AdminLTE App -->
    <script src="<?= base_url('theme_admin/dist/js/adminlte.min.js') ?>"></script>
    <script>
        var apiProductsCartUrl = '<?= url_to('api.productsCart') ?>';
        var apiCategoriesUrl = '<?= url_to('api.category') ?>';
        var apiCategoriesProductUrl = '<?= url_to('product.index') ?>';
    </script>
    <?= View('partials/sweetalert') ?>
    <script>
        $(document).ready(function() {
            $('.view-order-details').on('click', function() {
                const orderCode = $(this).data('codigo');
                $.ajax({
                    url: '<?= url_to('order.integra', '') ?>' + orderCode,
                    method: 'GET',
                    success: function(response) {
                        $('#orderDetailsContent').html(response);
                        $('#orderDetailsModal').modal('show');
                        console.log($('#orderDetailsModal').html());
                    }
                });
            });
        });
    </script>
</body>

</html>