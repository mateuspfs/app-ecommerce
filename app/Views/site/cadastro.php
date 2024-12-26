<?= $this->extend('site/master') ?>

<?= $this->section('title') ?>
    Cadastro
<?= $this->endSection() ?>

<?= $this->section('style') ?>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<?= $this->endSection() ?>

<?= $this->section('content') ?>  

   <div class="login-box">
    <!-- /.login-logo -->
    <div class="card card-outline card-primary">
      <div class="card-header text-center">
        <a href="" class="h1"><b>Cadastro</b></a>
      </div>
      <div class="card-body">
        <form action="<?= url_to('user.store') ?>" method="post" x-data id="form">  

          <div class="input-group mb-3">
            <div class="input-group-prepend">
              <span class="input-group-text" id="">Nome</span>
            </div>
            <input type="text" class="form-control" name="nome" value="<?= old('nome') ?>" >
          </div>

          <div class="input-group mb-3">
            <div class="input-group-prepend">
              <span class="input-group-text" id="">CPF</span>
            </div>
            <input type="text" class="form-control" name="cpf" id="cpf" value="<?= old('cpf') ?>" x-mask="999.999.999-99">
          </div>

          <div class="input-group mb-3">
            <div class="input-group-prepend">
              <span class="input-group-text" id="">Email</span>
            </div>
            <input type="email" class="form-control" name="email" value="<?= old('email') ?>">
          </div>

          <div class="input-group mb-3">
            <div class="input-group-prepend">
              <span class="input-group-text" id="">Telefone</span>
            </div>
            <input type="text" class="form-control" name="telefone" value="<?= old('telefone') ?>" x-mask="(99) 99999-9999">
          </div>

          <div class="input-group mb-3">
            <div class="input-group-prepend">
              <span class="input-group-text" id="">Senha</span>
            </div>
            <input type="password" class="form-control" name="password">
          </div>

          <div class="input-group mb-3">
            <div class="input-group-prepend">
              <span class="input-group-text" id="">Confirme sua senha</span>
            </div>
            <input type="password" class="form-control" name="cpassword">
          </div>

          <!-- /.col -->
          <div class="text-center">
              <button type="submit" class="btn btn-primary btn-block" type="submit">Cadastro</button>
          </div>
          <!-- /.col -->
  
          <p class="mb-1 mt-2">
            <a href="<?= url_to('auth.login') ?>" >Já possui uma conta?</a>
          </p>
          
        </form>
      </div>
      <!-- /.card-body -->
    </div>
    <!-- /.card -->
  </div>
  <!-- /.login-box -->

<?= $this->endSection() ?>  

<?= $this->section('script') ?>

  <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/mask@3.x.x/dist/cdn.min.js"></script>
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <script>
      $('#form').on('submit', function(e) {
        e.preventDefault();
        
        var cpf = $('#cpf').val().replace(/\D/g, '');
        $('#cpf').val(cpf);

        this.submit(); 
      });
  </script>
<?= $this->endSection() ?>