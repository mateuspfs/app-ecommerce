<?= $this->extend('admin/auth/master') ?>

<?= $this->section('content') ?>  

  <div class="login-box">
    <!-- /.login-logo -->
    <div class="card card-outline card-primary">
      <div class="card-header text-center">
        <a href="" class="h1"><b>Login</b></a>
      </div>
      <div class="card-body">
        <form action="<?= url_to('authAdmin.store') ?>" method="post">
          <div class="input-group mb-3">
            <input type="email" class="form-control" placeholder="Email" name="email" value="">
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-envelope"></span>
              </div>
            </div>
          </div>
          <div class="input-group mb-3">
            <input type="password" class="form-control" placeholder="Password" name="password">
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-lock"></span>
              </div>
            </div>
          </div>

          <!-- /.col -->
          <div class="text-center">
              <button type="submit" class="btn btn-primary btn-block" type="submit">Login</button>
          </div>
          <!-- /.col -->

        </form>

        <p class="mb-1">
          <a href="<?= url_to('authAdmin.forgotPassword') ?>" >Esqueci minha senha</a>
        </p>
      </div>
      <!-- /.card-body -->
    </div>
    <!-- /.card -->
  </div>
  <!-- /.login-box -->

<?= $this->endSection() ?>  
