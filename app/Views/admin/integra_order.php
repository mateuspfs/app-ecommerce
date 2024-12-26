<div class="container">
    <div class="row mb-4 pt-3 align-items-center">
        <div class="col">
            <h1 class="fw-bold">#<?= $order->codigo ?></h1>
        </div>
        <div class="col text-end">
            <?php if ($order->status === 'Pago') : ?>
                <a href="<?= url_to('order.update', $order->codigo) ?>" class="btn btn-success">Enviado para cliente</a>
            <?php endif ?>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h2 class="card-title mb-0 fw-bold">Informações do cliente</h2>
        </div>
        <div class="card-body">
            <p><strong>Nome:</strong> <?= $user->nome ?></p>
            <p><strong>CPF:</strong> <?= mask_cpf($user->cpf) ?></p>
            <p><strong>Email:</strong> <?= $user->email ?></p>
            <p><strong>Telefone:</strong> <?= mask_telefone($user->telefone) ?></p>
        </div>
    </div>

    <div class="card mb-4">
        <?php $endereco = json_decode($order->endereco) ?>
        <div class="card-header">
            <h2 class="card-title mb-0 fw-bold">Informações da entrega</h2>
        </div>
        <div class="card-body">
            <p><strong>CEP:</strong> <?= $endereco->cep ?></p>
            <p><strong>Rua:</strong> <?= $endereco->rua ?></p>
            <p><strong>Bairro:</strong> <?= $endereco->bairro ?></p>
            <p><strong>Cidade:</strong> <?= $endereco->cidade ?></p>
            <p><strong>Estado:</strong> <?= $endereco->uf ?></p>
            <p><strong>Complemento:</strong> <?= $endereco->complemento ?></p>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h2 class="card-title mb-0 fw-bold">Pagamento</h2>
        </div>
        <div class="card-body">
            <?php if ($order->cupomId) : ?>
                <p><strong>Cupom utilizado:</strong> <?= $order->cupom ?></p>
                <p class="text-success">Desconto de <?= ($cupom->tipo === 'f') ? 'R$' . mask_valor($cupom->desconto) : intval($cupom->desconto) . '%' ?> aplicado.</p>
            <?php endif ?>
            <p><strong>Pagamento Via:</strong> <?php if ($order->method === 'pix') { echo 'Pix'; } elseif ($order->method === 'boleto') { echo 'Boleto'; } else {  echo 'Cartão de Crédito ';} ?></p>
            <p><strong>Status do pagamento:</strong> <?= $order->statusPag ?></p>
            <p><strong>Subtotal:</strong> R$ <?= esc(mask_valor($subtotal)) ?></p>
            <p><strong>Valor total:</strong> R$ <?= esc(mask_valor($valorTotal)) ?></p>
        </div>
    </div>  

    <div class="card mb-4">
        <div class="card-header">
            <h2 class="card-title mb-0 fw-bold">Produtos do pedido</h2>
        </div>
        <div class="card-body">
            <table class="table pb-3">
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Preço</th>
                        <th>Quantidade</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product) : ?>
                        <tr>
                            <td><?= esc($product->nome) ?></td>
                            <td>R$ <?= esc(mask_valor($product->preco)) ?></td>
                            <td><?= esc($product->quantidade) ?></td>
                            <td>R$ <?= esc(mask_valor($product->preco * $product->quantidade)) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>