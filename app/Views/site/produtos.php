<?= $this->extend('site/master') ?>

<?= $this->section('title') ?>
Produtos
<?= $this->endSection() ?>

<?= $this->section('style') ?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

    <div class="main">
        <div class="container">
            <form action="<?= url_to('product.index') ?>" method="GET" class="p-4 bg-light border rounded">
                <div class="row mb-3">
                    <!-- Campo de busca grande -->
                    <div class="col-md-8">
                        <input type="text" class="form-control form-control-lg border border-dark" placeholder="Busque produtos..." name="search" value="<?= isset($_GET['search']) ? $_GET['search'] : '' ?>">
                    </div>

                    <!-- Botão de busca e reset -->
                    <div class="col-md-2 d-flex">
                        <button type="submit" class="btn btn-outline-secondary bg-white border border-dark me-2" id="button-addon2">
                            <img src="<?= base_url('theme_admin/docs/assets/img/lupa.png') ?>" width="20px" alt="Lupa">
                        </button>
                        <a href="<?= url_to('product.index') ?>" class="btn btn-outline-secondary bg-white border border-dark" id="button-addon2">
                            <img src="<?= base_url('theme_admin/docs/assets/img/reset.png') ?>" width="20px" alt="Reset">
                        </a>
                    </div>
                </div>

                <div class="row">
                    <!-- Categoria -->
                    <div class="col-md-3 mb-3">
                        <select name="categoria" class="form-select form-select-lg border border-dark">
                            <option value="" hidden selected>Selecione uma categoria</option>
                            <?php foreach ($categorias as $categoria) : ?>
                                <option value="<?= $categoria->id ?>" <?= isset($_GET['categoria']) && $_GET['categoria'] === $categoria->id ? 'selected' : '' ?>><?= $categoria->nome ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>

                    <!-- Preço Mínimo -->
                    <div class="col-md-3 mb-3">
                        <input type="text" name="min_preco" class="form-control form-control-lg border border-dark input-money" placeholder="Preço Mín." value="<?= isset($_GET['min_preco']) ? $_GET['min_preco'] : '' ?>">
                    </div>

                    <!-- Preço Máximo -->
                    <div class="col-md-3 mb-3">
                        <input type="text" name="max_preco" class="form-control form-control-lg border border-dark input-money" placeholder="Preço Máx." value="<?= isset($_GET['max_preco']) ? $_GET['max_preco'] : '' ?>">
                    </div>

                    <!-- Ordenação -->
                    <div class="col-md-3 mb-3">
                        <select name="order" class="form-select form-select-lg border border-dark">
                            <option value="" hidden selected>Ordenar por</option>
                            <option value="asc" <?= isset($_GET['order']) && $_GET['order'] === 'asc' ? 'selected' : '' ?>>Menor preço</option>
                            <option value="desc" <?= isset($_GET['order']) && $_GET['order'] === 'desc' ? 'selected' : '' ?>>Maior preço</option>
                        </select>
                    </div>
                </div>
            </form>

            <div class="row shop_box-top mt-5">
                <?php foreach ($produtos as $produto) : ?>
                    <div class="col-md-3 shop_box">
                        <a href="<?= url_to('product.integra', $produto->slug) ?>">
                            <img src="<?= asset($produto->img) ?>" class="img-responsive" alt="" />
                            <?php if (strtotime($produto->created_at) > strtotime('-7 days')) : ?>
                                <span class="new-box">
                                    <span class="new-label">Novo</span>
                                </span>
                            <?php endif ?>
                            <?php if ($produto->estoque < 20) : ?>
                                <span class="sale-box">
                                    <span class="sale-label">Esgotando!</span>
                                </span>
                            <?php endif ?>
                            <div class="shop_desc">
                                <h3><a href="<?= url_to('product.integra', $produto->slug) ?>"><?= mask_title($produto->nome) ?></a></h3>
                                <p><?= mask_desc($produto->descricao) ?></p>
                                <?php if ($produto->preco_comparativo) : ?>
                                    <span class="reducedfrom">R$ <?= mask_valor($produto->preco_comparativo )?></span>
                                    <span class="actual">R$<?= mask_valor($produto->preco) ?></span><br>
                                <?php else : ?>
                                    <span class="actual text-dark">R$<?= mask_valor($produto->preco) ?></span><br>
                                <?php endif ?>
                                <ul class="buttons">
                                    <li class="cart">
                                        <form action="<?= url_to('cart.store')?>" method="POST" class="add-to-cart-form">
                                            <input type="hidden" name="produtoId" value="<?= $produto->id?>">
                                            <input type="hidden" name="quantidade" value="1">
                                            <button type="submit">Adicionar ao carrinho</button>
                                        </form>
                                    </li>
                                    <li class="shop_btn"><a href="<?= url_to('product.integra', $produto->slug) ?>">Ver mais</a></li>
                                    <div class="clear"></div>
                                </ul>
                            </div>
                        </a>
                    </div>
                <?php endforeach ?>
                <nav class="d-flex justify-content-center">
                    <ul class="pagination">
                        <li class="page-item <?= $pagination->getCurrentPage() == 1 ? 'disabled' : '' ?>">
                            <a class="page-link bg-dark text-white" href="<?= $pagination->getPreviousPageURI() ?>">Anterior</a>
                        </li>

                        <li class="page-item <?= $pagination->getCurrentPage() == 1 ? 'active' : '' ?>">
                            <a class="page-link bg-dark text-white" href="<?= $pagination->getPageURI(1) ?>">1</a>
                        </li>

                        <?php if ($pagination->getCurrentPage() > 4) : ?>
                            <li class="page-item disabled">
                                <span class="page-link bg-dark text-white">...</span>
                            </li>
                        <?php endif; ?>

                        <?php for ($page = max(2, $pagination->getCurrentPage() - 2); $page <= min($pagination->getPageCount() - 1, $pagination->getCurrentPage() + 2); $page++) : ?>
                            <li class="page-item <?= $page == $pagination->getCurrentPage() ? 'active' : '' ?>">
                                <a class="page-link bg-dark text-white" href="<?= $pagination->getPageURI($page) ?>"><?= $page ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($pagination->getCurrentPage() <  $pagination->getPageCount() - 3) : ?>
                            <li class="page-item disabled">
                                <span class="page-link bg-dark text-white">...</span>
                            </li>
                        <?php endif; ?>

                        <?php if ($pagination->getPageCount() > 1) : ?>
                            <li class="page-item <?= $pagination->getCurrentPage() ==  $pagination->getPageCount() ? 'active' : '' ?>">
                                <a class="page-link bg-dark text-white" href="<?= $pagination->getPageURI($pagination->getPageCount()) ?>"><?= $pagination->getPageCount() ?></a>
                            </li>
                        <?php endif ?>

                        <li class="page-item <?= $pagination->getCurrentPage() ==  $pagination->getPageCount() ? 'disabled' : '' ?>">
                            <a class="page-link bg-dark text-white" href="<?= $pagination->getNextPageURI() ?>">Próxima</a>
                        </li>
                    </ul>
                </nav>
                <p class="d text-center text-muted">Página <?= $pagination->getCurrentPage() ?> de <?= $pagination->getPageCount() ?>.</p>
            </div>
        </div>
    </div>

<?= $this->endSection() ?>

<?= $this->section('script') ?>

<script src="<?= base_url('assets/js/helper-public.js') ?>"></script> 
<script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/mask@3.x.x/dist/cdn.min.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<?= $this->endSection() ?>