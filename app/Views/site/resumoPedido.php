<?= $this->extend('site/master') ?>

<?= $this->section('title') ?>
Carrinho
<?= $this->endSection() ?>

<?= $this->section('style') ?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
<link rel="stylesheet" href="<?= base_url('assets/css/cart.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/order.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="swiper" style="margin-bottom: 90px;">
    <div class="swiper-wrapper">
        <div class="swiper-slide">

            <form action="<?= url_to('order.store') ?>" method="post" style="padding-bottom: 20px">
                <?php csrf_token() ?>
                <h1 class="d-flex justify-content-center mt-4 mb-4"><strong>Produtos</strong></h1>
                <div class="basket" style="margin-left: 100px;">
                    <div class="basket-labels">
                        <ul>
                            <li class="item item-heading">Item</li>
                            <li class="price">Preço</li>
                            <li class="quantity">Quantidade</li>
                            <li class="subtotal">Subtotal</li>
                        </ul>
                    </div>
                    <?php foreach ($produtos as $produto) : ?>
                        <div class="basket-product mb-4">
                            <div class="item">
                                <div class="product-image-cart">
                                    <a href="<?= url_to('product.integra', $produto->slug) ?>">
                                        <img src="<?= asset($produto->img) ?>" alt="Placeholder Image 2" class="product-frame">
                                    </a>
                                </div>
                                <div class="product-details">
                                    <h1><strong>Código do produto - <?= $produto->id ?></strong></h1>
                                    <p><strong><span class="item-quantity"><?= $produto->quantidade ?></span>x <a style="color: #666" href="<?= url_to('product.integra', $produto->slug) ?>"><?= mask_title($produto->nome) ?></a></strong></p>
                                </div>
                            </div>
                            <div class="price"><?=  $produto->preco ?></div>
                            <div class="quantity quantity-control-cart">
                                <input type="number" name="quantidade" class="quantity-field" value="<?= $produto->quantidade ?>" min="1" readonly>
                            </div>
                            <div class="subtotal"><?=  $produto->quantidade * $produto->preco ?></div>
                        </div>
                    <?php endforeach ?>
                    <div class="basket-module mb-4">
                        <label for="promo-code">Código promocional</label>
                        <input id="promo-code" type="text" name="cupom" class="promo-code-field form-control border border-dark" value="">
                        <button class="promo-code-cta" type="button">Aplicar</button>
                    </div>
                    <div class="credit-module mb-4 p-3 border rounded bg-light d-flex align-items-center justify-content-between">
                        <div class="credit-info d-flex align-items-center">
                            <span class="font-weight-bold mr-2">Crédito disponível:</span>
                            <span class="font-weight-bold text-success">R$ <span id="credit-amount"><?=  $credito ?></span></span>
                        </div>

                        <button id="apply-credit-btn" class="btn btn-primary" type="button" <?= ($credito <= 0 ) ? 'disabled' : ''?>>
                            <i class="fas fa-money-check-alt mr-2"></i> Usar Crédito
                        </button>

                        <input type="hidden" id="credit-usage" name="credit-usage" value="0">
                    </div>
                    <div class="mb-2 mt-3">
                        <div class="summary-total-items"><span class="total-items"></span> Produtos no carrinho</div>
                        <div class="summary-subtotal">
                            <div class="subtotal-title">Subtotal</div>
                            <div class="subtotal-value final-value" id="basket-subtotal"><?=  $valorTotal ?></div>
                            <div class="summary-promo hide">
                                <div class="promo-title">Código Promocional</div>
                                <div class="promo-value final-value" id="basket-promo"></div>
                            </div>
                            <div class="summary-credit hide">
                                <div class="credit-title">Crédito usado</div>
                                <div class="credit-value final-value" id="basket-credit"></div>
                            </div>
                        </div>
                        <div class="summary-total mt-3">
                            <div class="total-title">Total</div>
                            <div class="total-value final-value" id="basket-total"><?=  $valorTotal ?></div>
                        </div>
                    </div>
                </div>
                <div class="navigation-buttons mt-4 mb-1" style="margin-left: 180px;margin-bottom: 500px;">
                    <button class="prev-button" type="button">
                        <a href="<?= url_to("cart.index") ?>" class="prev_a">
                            < Carrinho
                        </a>
                    </button>
                    <button class="next-button" type="button">Próximo</button>
                </div>
        </div>

        <div class="swiper-slide">
            <h1 class="d-flex justify-content-center mt-4 mb-4"><strong>Endereço de entrega</strong></h1>

            <div class="form-container">
                <div class="row g-3" x-data>
                    <div class="col-12">
                        <label for="nome" class="form-label">Nome do recebedor</label>
                        <input type="text" class="form-control" id="nome" name="nome" value="">
                    </div>
                    <div class="col-12">
                        <label for="cep" class="form-label">CEP</label>
                        <input type="text" x-mask='99999-999' class="form-control" id="cep" name="cep" onblur="pesquisacep(this.value)" value="">
                    </div>
                    <div class="col-12">
                        <label for="rua" class="form-label">Rua</label>
                        <input type="text" class="form-control" id="rua" name="rua" readonly>
                    </div>
                    <div class="col-md-6">
                        <label for="bairro" class="form-label">Bairro</label>
                        <input type="text" class="form-control" id="bairro" name="bairro" readonly>
                    </div>
                    <div class="col-md-4">
                        <label for="cidade" class="form-label">Cidade</label>
                        <input type="text" class="form-control" id="cidade" name="cidade" readonly>
                    </div>
                    <div class="col-md-2">
                        <label for="uf" class="form-label">Estado</label>
                        <input type="text" class="form-control" id="uf" name="uf" readonly>
                    </div>
                    <div class="col-12">
                        <label for="numero" class="form-label">Número</label>
                        <input type="text" class="form-control" id="numero" name="numero" value="">
                    </div>
                    <div class="col-12">
                        <label for="complemento" class="form-label">Complemento</label>
                        <input type="text" class="form-control" id="complemento" name="complemento" value="">
                    </div>
                </div>
            </div>
            <div class="navigation-buttons mt-4 mb-1">
                <button class="prev-button" type="button">Voltar</button>
                <button class="next-button" type="button">Próximo</button>
            </div>
        </div>
        <div class="swiper-slide">
            <h1 class="d-flex justify-content-center mt-4 mb-4"><strong>Pagamento</strong></h1>

            <div class="form-container">
                <div x-data="{ paymentMethod: 'credit-card' }">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="paymentMethod" id="creditCard" value="credit-card" x-model="paymentMethod">
                        <label class="form-check-label" for="creditCard">
                            Cartão de Crédito
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="paymentMethod" id="pix" value="pix" x-model="paymentMethod">
                        <label class="form-check-label" for="pix">
                            PIX
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="paymentMethod" id="boleto" value="boleto" x-model="paymentMethod">
                        <label class="form-check-label" for="boleto">
                            Boleto
                        </label>
                    </div>

                    <!-- Campos para Cartão de Crédito -->
                    <div x-show="paymentMethod === 'credit-card'" class="mt-4">
                        <div class="mb-3">
                            <label for="cardName" class="form-label">Nome no Cartão</label>
                            <input type="text" class="form-control" id="nome_c" value="">
                        </div>
                        <div class="mb-3">
                            <label for="cardNumber" class="form-label">Número do Cartão</label>
                            <input type="text" class="form-control" id="numero_c" x-mask="9999 9999 9999 9999" value="">
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="cardExpiry" class="form-label">Validade</label>
                                <input type="text" class="form-control" id="validade_c" x-mask="99/99" value="">
                            </div>
                            <div class="col-md-6">
                                <label for="cardCVC" class="form-label">CVV</label>
                                <input type="text" class="form-control" id="cvv_c" x-mask="999" placeholder="123">
                            </div>
                            <input type="hidden" name="crypted_card" id="crypted_card">
                        </div>
                    </div>

                    <!-- Campos para PIX -->
                    <div x-show="paymentMethod === 'pix'" class="mt-4">
                        <div class="alert alert-info">
                            Selecionado PIX.
                        </div>
                    </div>

                    <!-- Campos para PIX -->
                    <div x-show="paymentMethod === 'boleto'" class="mt-4">
                        <div class="alert alert-info">
                            Selecionado Boleto.
                        </div>
                    </div>
                </div>
            </div>
            <div class="navigation-buttons mt-4 mb-1">
                <button class="prev-button" type="button">Voltar</button>
                <button id="submit" type="submit">Concluído</button>
            </div>
            </form>
        </div>
        <div class="navigation-buttons">
            <button class="prev-button" type="button">Voltar</button>
        </div>
    </div>
    <div class="swiper-pagination"></div>
</div>

<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script>
    var apiVerifyCupom = '<?= url_to("cupom.verify") ?>';
    var apiGetPublicKey = '<?= url_to("payment.getPublicKey") ?>';
</script>
<script src="https://cdn.jsdelivr.net/npm/@alpinejs/mask@3.x.x/dist/cdn.min.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js" defer></script>
<script src="https://assets.pagseguro.com.br/checkout-sdk-js/rc/dist/browser/pagseguro.min.js" defer></script>
<script src="<?= base_url('assets/js/order.js') ?>"></script>

<?= $this->endSection() ?>