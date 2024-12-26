<?= $this->extend('admin/auth/master') ?>

<?= $this->section('content') ?>  

  <div class="login-box">
    <!-- /.login-logo -->
    <div class="card card-outline card-primary">
      <div class="card-header text-center">
        <a href="" class="h2"><b>Redefinição de Senha</b></a>
      </div>
      <div class="card-body">

        <form action="<?= url_to('authAdmin.resetPasswordSubmit') ?>" method="post" class="m-2">
          <div class="input-group mb-3">
            <input type="hidden" name="token" value="<?= $_GET['token'] ?>" >
            <input type="hidden" name="user_email" value="<?= $_GET['user_mail'] ?>" >
            <input type="password" class="form-control" placeholder="Senha nova" name="password">
            <div class="input-group-append">    
              <div class="input-group-text">
                <span class="fas fa-lock"></span>
              </div>
            </div>
          </div>
          <div class="input-group mb-3">
            <input type="password" class="form-control" placeholder="Confirme sua senha" name="cpassword">
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-lock"></span>
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
