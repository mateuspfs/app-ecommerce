<?= $this->extend('site/master') ?>

<?= $this->section('title') ?>  
  Login
<?= $this->endSection() ?>

<?= $this->section('style') ?>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous"> 
<?= $this->endSection() ?>

<?= $this->section('content') ?>  

  <div class="login-box">
    <!-- /.login-logo -->
    <div class="card card-outline card-primary">
      <div class="card-header text-center">
        <a href="" class="h1"><b>Login</b></a>
      </div>
      <div class="card-body">
        <form action="<?= url_to('auth.store') ?>" method="post">
          <div class="input-group mb-3">
            <input type="email" class="form-control" placeholder="Email" name="email" value="<?= old('email') ?>">
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
          <p class="">
            <a href="<?= url_to('auth.forgotPassword') ?>" >Esqueci minha senha</a>
          </p>

          <!-- /.col -->
          <div class="text-center">
              <button type="submit" class="btn btn-primary btn-block" type="submit">Login</button>
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
