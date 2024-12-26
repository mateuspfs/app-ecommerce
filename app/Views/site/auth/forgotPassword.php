<?= $this->extend('site/master') ?>

<?= $this->section('title') ?>  
  Esqueci a senha 
<?= $this->endSection() ?>

<?= $this->section('content') ?>  

  <div class="login-box">
    <!-- /.login-logo -->
    <div class="card card-outline card-primary">
      <div class="card-header text-center">
        <a href="" class="h1"><b>Esqueci minha senha</b></a>
      </div>
      <div class="card-body">
      <p class="login-box-msg">Insira seu email para receber a redefinição de senha</p>

        <form action="<?= url_to('auth.forgotPassword') ?>" method="post">
          <div class="input-group mb-3">
            <input type="email" class="form-control" placeholder="Email" name="email">
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-envelope"></span>
              </div>
            </div>
          </div>

          <!-- /.col -->
          <div class="text-center">
              <button type="submit" class="btn btn-primary btn-block" type="submit">Concluir</button>
          </div>
          <!-- /.col -->

        </form>
      </div>
      <!-- /.card-body -->
    </div>
    <!-- /.card -->
  </div>
  <!-- /.login-box -->

<?= $this->endSection() ?>  
