<?= $this->extend('site/master') ?>

<?= $this->section('title') ?>
Carrinho
<?= $this->endSection() ?>

<?= $this->section('style') ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="<?= base_url('assets/css/cart.css')?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

    <div class="basket">
        <div class="basket-labels">
            <ul>
                <li class="item item-heading">Item</li>
                <li class="price">Preço</li>
                <li class="quantity">Quantidade</li>
                <li class="subtotal">Subtotal</li>
            </ul>
        </div>
        <?php foreach($produtos as $produto): ?>
            <div class="basket-product mb-4">
                <div class="item">
                    <div class="product-image-cart">
                        <a href="<?= url_to('product.integra', $produto->slug) ?>">
                            <img src="<?= asset($produto->img) ?>" alt="Placholder Image 2" class="product-frame">
                        </a>
                    </div>
                    <div class="product-details">
                        <h1><strong>Código do produto - <?= $produto->id ?></strong></h1>
                        <p><strong><span class="item-quantity"><?= $produto->quantidade ?></span>x <a style="color: #666" href="<?= url_to('product.integra', $produto->slug) ?>"><?= mask_title($produto->nome) ?></a></strong></p>
                    </div> 
                </div>
                <div class="price"><?= $produto->preco ?></div>
                <div class="quantity quantity-control-cart">
                    <form class="quantity-form" action="<?= url_to('cart.update') ?>" method="POST">
                        <?= csrf_field() ?>
                        <input type="hidden" name="productId" value="<?= $produto->id ?>">
                        <button type="button" class="quantity-btn-cart decrease-qty">-</button>
                        <input type="number" name="quantity" class="quantity-field" value="<?= $produto->quantidade ?>" min="1" readonly>
                        <button type="button" class="quantity-btn-cart increase-qty">+</button>
                    </form>
                </div>
               <div class="subtotal"><?= $produto->quantidade * $produto->preco ?></div>
                <div class="remove">
                    <form class="remove-form" action="<?= url_to('cart.destroy', $produto->id)?>" method="POST">
                        <?= csrf_field() ?>
                        <input type="hidden" name="_method" value="DELETE">
                        <button>Remover</button>
                    </form>
                </div>
            </div>
        <?php endforeach ?>
        <div class="clean-cart">
            <?php if(!empty($produtos)): ?>
                <form action="<?= url_to('cart.clean') ?>" method="POST">
                    <?= csrf_field() ?>
                    <input type="hidden" name="_method" value="DELETE">
                    <button class="btn btn-secondary">Limpar Carrinho</button>
            </form>
            <?php endif ?>
        </div>
    </div>
    <aside>
        <div class="summary">
            <div class="summary-total-items"><span class="total-items"></span> Produtos no carrinho</div>
            <div class="summary-subtotal">
                <div class="subtotal-title">Subtotal</div>
                <div class="subtotal-value final-value" id="basket-subtotal"><?= $valorTotal ?></div>
                <div class="summary-promo hide">
                    <div class="promo-title">Desconto</div>
                    <div class="promo-value final-value" id="basket-promo"></div>
                </div>
            </div>
            <!-- <div class="summary-delivery">
                <select name="delivery-collection" class="summary-delivery-selection">
                    <option value="0" selected="selected">Select Collection or Delivery</option>
                    <option value="collection">Collection</option>
                    <option value="first-class">Royal Mail 1st Class</option>
                    <option value="second-class">Royal Mail 2nd Class</option>
                    <option value="signed-for">Royal Mail Special Delivery</option>
                </select>
            </div> -->
            <div class="summary-total mt-3">
                <div class="total-title">Total</div>
                <div class="total-value final-value" id="basket-total"><?= $valorTotal ?></div>
            </div>
            <div class="summary-checkout">
                <a class="checkout-cta btn btn-success" href="<?= url_to('order.resume') ?>">Prosseguir para o pedido</a>
            </div>
        </div>
    </aside>

<?= $this->endSection() ?>

<?= $this->section('script') ?>
    <script>
        var attProductUrl = '<?= url_to('cart.update') ?>';
    </script>

    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/mask@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="<?= base_url('assets/js/cart.js') ?>"></script>

<?= $this->endSection() ?>