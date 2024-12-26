<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= $this->renderSection('title'); ?></title>
  <?= $this->renderSection('style') ?>
  <!-- Bootstrap 5.3.3 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="<?= base_url('theme_admin/plugins/fontawesome-free/css/all.min.css') ?>">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?= base_url('theme_admin/dist/css/adminlte.min.css') ?>">
</head>

<body class="hold-transition sidebar-mini">
  <div class="wrapper">

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light fixed">
      <!-- Left navbar links -->
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
      </ul>

      <!-- Right navbar links -->
      <ul class="navbar-nav ml-auto">
        <!-- Navbar Search -->
        <!-- <li class="nav-item">
      <a class="nav-link" data-widget="navbar-search" href="#" role="button">
        <i class="fas fa-search"></i>
      </a>
      <div class="navbar-search-block">
        <form class="form-inline">
          <div class="input-group input-group-sm">
            <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
            <div class="input-group-append">
              <button class="btn btn-navbar" type="submit">
                <i class="fas fa-search"></i>
              </button>
              <button class="btn btn-navbar" type="button" data-widget="navbar-search">
                <i class="fas fa-times"></i>
              </button>
            </div>
          </div>
        </form>
      </div>
    </li> -->

        <li class="nav-item">
          <a class="nav-link" data-widget="fullscreen" href="#" role="button">
            <i class="fas fa-expand-arrows-alt"></i>
          </a>
        </li>
      </ul>
    </nav>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4 position-fixed h-100">
      <!-- Brand Logo -->
      <a href="#" class="brand-link">
        <span class="brand-text font-weight-light">Ecommerce</span>
      </a>

      <!-- Sidebar -->
      <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
          <div class="image">
            <!-- <img src="dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image"> -->
          </div>
          <div class="info">
            <a href="#" class="d-block">
              <?= session()->get('admin')->nome ?>
            </a>
          </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
          <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <li class="nav-item">
              <a href="<?= url_to('admin.index') ?>" class="nav-link">
                <img src="<?= base_url('theme_admin/docs/assets/img/admins.png') ?>" alt="" width="35px">
                <p>
                  Admins
                </p>
              </a>
            </li>
            <li class="nav-item">
              <a href="<?= url_to('user.index') ?>" class="nav-link">
                <img src="<?= base_url('theme_admin/docs/assets/img/users.png') ?>" alt="" width="35px">
                <p>
                  Usuários
                </p>
              </a>
            </li>
            <li class="nav-item">
              <a href="<?= url_to('admin.order.index') ?>" class="nav-link">
                <img src="<?= base_url('theme_admin/docs/assets/img/order.png') ?>" alt="" width="35px">
                <p>
                  Pedidos
                </p>
              </a>
            </li>
            <li class="nav-item">
              <a href="<?= url_to('admin.product.index') ?>" class="nav-link">
                <img src="<?= base_url('theme_admin/docs/assets/img/products.png') ?>" alt="" width="35px">
                <p>
                  Produtos
                </p>
              </a>
            </li>
            <li class="nav-item">
              <a href="<?= url_to('category.index') ?>" class="nav-link">
                <img src="<?= base_url('theme_admin/docs/assets/img/categories.png') ?>" alt="" width="35px">
                <p>
                  Categorias
                </p>
              </a>
            </li>
            <li class="nav-item">
              <a href="<?= url_to('cupom.index') ?>" class="nav-link">
                <img src="<?= base_url('theme_admin/docs/assets/img/cupom.png') ?>" alt="" width="35px">
                <p>
                  Cupons
                </p>
              </a>
            </li>

            <li class="nav-item" style="margin-top: 140px;">
              <a data-toggle="modal" data-target="#logout" class="nav-link">
                <img src="<?= base_url('theme_admin/docs/assets/img/logout.png') ?>" alt="" width="40px">
                <p>
                  Sair
                </p>
              </a>
            </li>
          </ul>
        </nav>
        <!-- /.sidebar-menu -->
      </div>
      <!-- /.sidebar -->
    </aside>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <?= $this->renderSection('content') ?>
    </div>
    <!-- /.content-wrapper -->

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
      <!-- Control sidebar content goes here -->
      <div class="p-3">
        <h5>Title</h5>
        <p>Sidebar content</p>
      </div>
    </aside>
    <!-- /.control-sidebar -->
  </div>

  <div class="modal fade" id="logout" tabindex="-1" aria-labelledby="logout" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content p-4">
        <div class="modal-body text-center">
          <h2 class="display-6">Deseja sair?</h2>
          <div class="mt-5 d-flex text-center justify-content-center align-items-center">
            <button class="btn btn-lg btn-danger m-4" data-dismiss="modal">Não</button>
            <a class="btn btn-lg btn-success m-4" href="<?= url_to('authAdmin.logout') ?>">Sim</a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- REQUIRED SCRIPTS -->
  <!-- jQuery -->
  <script src="<?= base_url('theme_admin/plugins/jquery/jquery.min.js') ?>"></script>
  <!-- Bootstrap 4 -->
  <script src="<?= base_url('theme_admin/plugins/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
  <!-- AdminLTE App -->
  <script src="<?= base_url('theme_admin/dist/js/adminlte.min.js') ?>"></script>
  <!-- Masks alpine.js -->
  <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/mask@3.x.x/dist/cdn.min.js"></script>
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <!-- Helpers -->
  <script src="<?= base_url('assets/js/helper.js') ?>"></script> 
  <!-- Sweetie Alert -->
  <?= view('partials/sweetalert') ?>
  <?= $this->renderSection('script') ?>
</body>
</html>