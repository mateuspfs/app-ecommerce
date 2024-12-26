<?= $this->extend('site/master') ?>

<?= $this->section('title') ?>
    Integra
<?= $this->endSection() ?>

<?= $this->section('style') ?>

    <link href="<?= base_url('template_site/css/etalage.css')?>" rel="stylesheet" type="text/css">
    <script src="<?= base_url('template_site/js/jquery.min.js')?>"></script>
    <script src="<?= base_url('template_site/js/jquery.etalage.min.js')?>"></script>
    <script>
            jQuery(document).ready(function($){

                $('#etalage').etalage({
                    thumb_image_width: 300,
                    thumb_image_height: 400,
                    
                    show_hint: true,
                    click_callback: function(image_anchor, instance_id){
                        alert('Callback example:\nYou clicked on an image with the anchor: "'+image_anchor+'"\n(in Etalage instance: "'+instance_id+'")');
                    }
                });
                // This is for the dropdown list example:
                $('.dropdownlist').change(function(){
                    etalage_show( $(this).find('option:selected').attr('class'));
                });

        });
    </script>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

    <div class="main">
        <div class="shop_top">
            <div class="container">
                <div class="row">
                    <div class="col-md-9 single_left">
                        <div class="single_image">
                            <ul id="etalage">
                                <li>
                                    <img class="etalage_source_image" src="<?= asset($produto->img) ?>" />
                                </li>
                                <?php if(!empty($galery)): ?>
                                    <?php foreach($galery as $image): ?>
                                        <li>
                                            <img class="etalage_source_image" src="<?= asset($image->caminho) ?>" />
                                        </li>
                                    <?php endforeach ?>
                                <?php endif ?>
                            </ul>
                        </div>
                        
                        <!-- end product_slider -->
                        <div class="single_right">
                            <h3><?= $produto->nome ?> </h3>
                            <p class="m_10"></p>
                            <ul class="options">
                                <h4 class="m_12">Select a Size(cm)</h4>
                                <li><a href="#">151</a></li>
                                <li><a href="#">148</a></li>
                                <li><a href="#">156</a></li>
                                <li><a href="#">145</a></li>
                                <li><a href="#">162(w)</a></li>
                                <li><a href="#">163</a></li>
                            </ul>
                            <ul class="product-colors">
                                <h3>available Colors</h3>
                                <li><a class="color1" href="#"><span> </span></a></li>
                                <li><a class="color2" href="#"><span> </span></a></li>
                                <li><a class="color3" href="#"><span> </span></a></li>
                                <li><a class="color4" href="#"><span> </span></a></li>
                                <li><a class="color5" href="#"><span> </span></a></li>
                                <li><a class="color6" href="#"><span> </span></a></li>
                                <div class="clear"> </div>
                            </ul>
                            <div class="btn_form">
                                <form>
                                    <input type="submit" value="buy now" title="">
                                </form>
                            </div>
                            <ul class="add-to-links">
                                <li><img src="<?= base_url('template_site/images/wish.png')?>" alt=""><a href="#">Add to wishlist</a></li>
                            </ul>
                            <div class="social_buttons">
                                <h4>95 Items</h4>
                                <button type="button" class="btn1 btn1-default1 btn1-twitter" onclick="">
                                    <i class="icon-twitter"></i> Tweet
                                </button>
                                <button type="button" class="btn1 btn1-default1 btn1-facebook" onclick="">
                                    <i class="icon-facebook"></i> Share
                                </button>
                                <button type="button" class="btn1 btn1-default1 btn1-google" onclick="">
                                    <i class="icon-google"></i> Google+
                                </button>
                                <button type="button" class="btn1 btn1-default1 btn1-pinterest" onclick="">
                                    <i class="icon-pinterest"></i> Pinterest
                                </button>
                            </div>
                        </div>
                        <div class="clear"> </div>
                    </div>
                    <div class="col-md-3">
                        <div class="box-info-product">
                            <p class="price2">R$ <?= mask_valor($produto->preco) ?></p>
                            <form action="<?= url_to('cart.store') ?>" method="POST" class="add-to-cart-form">
                                <input type="hidden" name="produtoId" value="<?= $produto->id ?>">
                                <ul class="prosuct-qty">
                                    <span>Quantidade:</span>
                                    <div class="quantity-control">
                                        <button type="button" class="quantity-btn" id="decrease-qty">-</button>
                                            <input type="number" name="quantidade" id="quantity" value="1" min="1" max="100" readonly>
                                        <button type="button" class="quantity-btn" id="increase-qty">+</button>
                                    </div>
                                </ul>
                                <button type="submit" name="Submit" class="exclusive">
                                    <span>Add to cart</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="desc">
                    <h4>Descrição</h4>
                    <p><?= $produto->descricao ?></p>
                </div>
                <div class="row">
                    <h4 class="m_11">produtos parecidos caso se interesse</h4>
                    <?php foreach($recommended as $recommend): ?>
                        <div class="col-md-4 product1">
                            <a href="<?= url_to('product.integra', $recommend->slug)?>">
                                <img src="<?= asset($recommend->img)?>" class="img-responsive" alt="" />
                                <div class="shop_desc"><a href="<?= url_to('product.integra', $recommend->slug)?>">
                                    </a>
                                    <h3><a href="<?= url_to('product.integra', $recommend->slug)?>"><?= mask_title($recommend->nome) ?></a></h3>
                                    <p><?= mask_desc($recommend->descricao) ?></p>
                                    <?php if ($recommend->preco_comparativo) : ?>
                                    <span class="reducedfrom">R$ <?= mask_valor($recommend->preco_comparativo) ?></span>
                                    <?php endif ?>
                                    <span class="actual">R$<?= mask_valor($recommend->preco) ?></span><br>
                                    <ul class="buttons">
                                        <li class="cart"><a href="#">Add To Cart</a></li>
                                        <li class="shop_btn"><a href="#">Read More</a></li>
                                        <div class="clear"> </div>
                                    </ul>
                                </div>
                            </a>
                        </div>
                    <?php endforeach ?>
                </div>
            </div>
        </div>
    </div>  

<?= $this->endSection() ?>  

<?= $this->section('script') ?>
    <script src="<?= base_url('assets/js/integra.js') ?>"></script>              
<?= $this->endSection() ?>