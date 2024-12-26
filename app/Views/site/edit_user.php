<?= $this->extend('site/master') ?>

<?= $this->section('title') ?>
    Cadastro
<?= $this->endSection() ?>

<?= $this->section('style') ?>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<?= $this->endSection() ?>

<?= $this->section('content') ?>  

<div class="container">
        <div class="text-center">
            <form class="form-signin" action="<?= url_to('user.update', $user->id) ?>" method="POST" x-data>
                <?= csrf_field() ?>

                <h1 class="h3 mb-3 font-weight-normal m-4">Atualize seus dados</h1>

                <label class="top-0 end-0">Nome</label>
                <input type="text" name="nome" value="<?= $user->nome ?>" class="form-control mx-auto border border-dark mb-1" style="width: 40%;" required>

                <label class="top-0 end-0">CPF</label>
                <input x-mask="999.999.999-99" type="text" name="cpf" value="<?= $user->cpf ?>" class="form-control mx-auto border border-dark mb-1" style="width: 40%;" required disabled>

                <label class="top-0 end-0">Telefone</label>
                <input x-mask="(99) 99999-9999" type="text" name="telefone" value="<?= $user->telefone ?>" class="form-control mx-auto border border-dark mb-1" style="width: 40%;" required>
                
                <label class="top-0 end-0">Email</label>
                <input type="email" name="email" value="<?= $user->email ?>" class="form-control mx-auto border border-dark mb-1" style="width: 40%;" required>
                
                <label class="top-0 end-0">Senha Nova</label>
                <input type="password" name="password" class="form-control mx-auto border border-dark mb-1" style="width: 40%;">
                
                <label class="top-0 end-0">Confirme a Senha</label>
                <input type="password" name="cpassword" class="form-control mx-auto border border-dark mb-1" style="width: 40%;">

                <div class="d-flex flex-column align-items-center">
                    <button class="btn btn-lg btn-success mt-3" style="width: 40%" type="submit">Finalizar</button>    
                </div>
            </form>
        </div>
    </div>

<?= $this->endSection() ?>  

<?= $this->section('script') ?>

  <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/mask@3.x.x/dist/cdn.min.js"></script>
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<?= $this->endSection() ?>