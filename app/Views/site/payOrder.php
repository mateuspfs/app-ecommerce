<?= $this->extend('site/master') ?>

<?= $this->section('title') ?>
Pagamento
<?= $this->endSection() ?>

<?= $this->section('style') ?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="container mt-5">
        <div class="card mx-auto" style="max-width: 400px;">
            <div class="card-header text-center">
                <h5 class="card-title">Pagamento Via <?= $returnPayment['method'] === 'pix' ? 'PIX' : 'Boleto' ?></h5>
            </div>
            <div class="card-body text-center">
                <?php if ($returnPayment['method'] === 'pix'): ?>
                    <p class="card-text">Use o QrCode abaixo para realizar o pix:</p>
                    <img src="<?= $returnPayment['qr_code'] ?>" class="img-fluid mt-3" alt="QR Code PIX">
                    <p class="card-text">Ou opte por copiar o código pix:</p>
                    <p class="card-text font-weight-bold"><?= $returnPayment['text'] ?></p>
                <?php elseif ($returnPayment['method'] === 'boleto'): ?>
                    <p class="card-text">Use o código de barras abaixo para pagar o boleto:</p>
                    <p class="card-text font-weight-bold"><strong><?= $returnPayment['text'] ?></strong></p>
                    <p class="card-text">Ou clique no link abaixo para visualizar o boleto em PDF:</p>
                    <a href="<?= $returnPayment['pdf'] ?>" class="btn btn-primary" target="_blank">Visualizar Boleto</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>

<?= $this->section('script') ?>

<?= $this->endSection() ?>
