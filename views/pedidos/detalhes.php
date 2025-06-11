<?php include BASE_PATH . '/views/layout/header.php'; ?>

<div class="row">
    <div class="col-12">
        <h1>Pedido #<?= $pedido['id'] ?></h1>
        <hr>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5>Status do Pedido</h5>
            </div>
            <div class="card-body">
                <span class="badge bg-success"><?= ucfirst($pedido['status']) ?></span>
            </div>
        </div>
        
        <div class="card mb-4">
            <div class="card-header">
                <h5>Dados do Cliente</h5>
            </div>
            <div class="card-body">
                <p><strong>Nome:</strong> <?= htmlspecialchars($pedido['cliente_nome']) ?></p>
                <p><strong>E-mail:</strong> <?= htmlspecialchars($pedido['cliente_email']) ?></p>
                <p><strong>Telefone:</strong> <?= htmlspecialchars($pedido['cliente_telefone']) ?></p>
            </div>
        </div>
        
        <div class="card mb-4">
            <div class="card-header">
                <h5>Endereço de Entrega</h5>
            </div>
            <div class="card-body">
                <p>
                    <?= htmlspecialchars($pedido['endereco']) ?>, <?= htmlspecialchars($pedido['numero']) ?><br>
                    <?= htmlspecialchars($pedido['complemento']) ?><br>
                    <?= htmlspecialchars($pedido['bairro']) ?><br>
                    <?= htmlspecialchars($pedido['cidade']) ?> - <?= htmlspecialchars($pedido['uf']) ?><br>
                    CEP: <?= htmlspecialchars($pedido['cep']) ?>
                </p>
            </div>
        </div>
        
        <div class="card mb-4">
            <div class="card-header">
                <h5>Itens do Pedido</h5>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th>Preço Unit.</th>
                            <th>Qtd.</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($itens as $item): ?>
                        <tr>
                            <td>
                                <?= htmlspecialchars($item['produto_nome']) ?>
                                <?php if ($item['variacao_nome']): ?>
                                    <br><small><?= htmlspecialchars($item['variacao_nome']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td>R$ <?= number_format($item['preco_unitario'], 2, ',', '.') ?></td>
                            <td><?= $item['quantidade'] ?></td>
                            <td>R$ <?= number_format($item['preco_total'], 2, ',', '.') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5>Resumo do Pedido</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <span>Subtotal:</span>
                    <span>R$ <?= number_format($pedido['subtotal'], 2, ',', '.') ?></span>
                </div>
                
                <?php if ($pedido['desconto'] > 0): ?>
                <div class="d-flex justify-content-between text-success">
                    <span>Desconto:</span>
                    <span>-R$ <?= number_format($pedido['desconto'], 2, ',', '.') ?></span>
                </div>
                <?php endif; ?>
                
                <div class="d-flex justify-content-between">
                    <span>Frete:</span>
                    <span><?= $pedido['frete'] == 0 ? 'GRÁTIS' : 'R$ ' . number_format($pedido['frete'], 2, ',', '.') ?></span>
                </div>
                
                <hr>
                
                <div class="d-flex justify-content-between">
                    <strong>Total:</strong>
                    <strong>R$ <?= number_format($pedido['total'], 2, ',', '.') ?></strong>
                </div>
                
                <hr>
                
                <div class="d-grid gap-2">
                    <a href="<?= BASE_URL ?>" class="btn btn-primary">Voltar às Compras</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="alert alert-success mt-4">
    <h4>Pedido realizado com sucesso!</h4>
    <p>Você receberá um e-mail de confirmação em breve.</p>
</div>

<?php include BASE_PATH . '/views/layout/footer.php'; ?> 