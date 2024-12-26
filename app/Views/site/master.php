<!--A Design by W3layouts
Author: W3layout
Author URL: http://w3layouts.com
License: Creative Commons Attribution 3.0 Unported
License URL: http://creativecommons.org/licenses/by/3.0/
-->
<!DOCTYPE HTML>
<html>

<head>
    <title><?= $this->renderSection('title') ?></title>
    <link href="<?= base_url('template_site/css/bootstrap.css') ?>" rel='stylesheet' type='text/css' />
    <link href="<?= base_url('template_site/css/style.css') ?>" rel='stylesheet' type='text/css' />
    <link rel="stylesheet" href="<?= base_url('theme_admin/plugins/fontawesome-free/css/all.min.css') ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700,800' rel='stylesheet' type='text/css'>
    <?= $this->renderSection('style') ?>
    <script type="application/x-javascript">
        addEventListener("load", function() {
            setTimeout(hideURLbar, 0);
        }, false);

        function hideURLbar() {
            window.scrollTo(0, 1);
        }
    </script>
    <script src="<?= base_url('assets/js/master-public.js') ?>" defer></script>
    <link href="<?= base_url('template_site/css/etalage.css') ?>" rel="stylesheet" type="text/css">
    <script src="<?= base_url('template_site/js/jquery.min.js') ?>"></script>
    <script src="<?= base_url('template_site/js/jquery.etalage.min.js') ?>"></script>
    <!-- start slider -->
    <link rel="stylesheet" href="<?= base_url('template_site/css/fwslider.css') ?>" media="all">
    <script src="<?= base_url('template_site/js/jquery-ui.min.js') ?>"></script>
    <script src="<?= base_url('template_site/js/fwslider.js') ?>"></script>
    <script type="text/javascript" src="<?= base_url('template_site/js/responsive-nav.js') ?>"></script>
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
                                        <!-- <li><a class="dropdown-item" type="button">Something else here</a></li> -->
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
                <?= $this->renderSection('content') ?>
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

    <script>
        var apiProductsCartUrl = '<?= url_to('api.productsCart') ?>';
        var apiCategoriesUrl = '<?= url_to('api.category') ?>';
        var apiCategoriesProductUrl = '<?= url_to('product.index') ?>';
    </script>
    <?= $this->renderSection('script') ?>
    <?= View('partials/sweetalert') ?>
</body>

</html>