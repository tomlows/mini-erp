<?php include BASE_PATH . '/views/layout/header.php'; ?>

<div class="row">
    <div class="col-12">
        <h1><i class="fas fa-shopping-cart"></i> Meu Carrinho</h1>
        <hr>
    </div>
</div>

<?php if (empty($itens)): ?>
    <div class="row">
        <div class="col-12">
            <div class="alert alert-info text-center">
                <i class="fas fa-shopping-cart fa-3x mb-3"></i>
                <h4>Seu carrinho está vazio</h4>
                <p>Adicione alguns produtos incríveis ao seu carrinho!</p>
                <a href="<?= BASE_URL ?>" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i> Continuar Comprando
                </a>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="row">
        <div class="col-lg-8">
            <?php foreach ($itens as $item): ?>
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-2">
                                <div class="bg-light rounded p-3 text-center">
                                    <i class="fas fa-image text-muted fa-2x"></i>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <h5 class="card-title mb-1"><?= htmlspecialchars($item['produto_nome']) ?></h5>
                                <?php if ($item['variacao_nome']): ?>
                                    <small class="text-muted">Variação: <?= htmlspecialchars($item['variacao_nome']) ?></small>
                                <?php endif; ?>
                            </div>
                            
                            <div class="col-md-2">
                                <label class="form-label small">Preço unitário</label>
                                <div class="fw-bold">R$ <?= number_format($item['preco_unitario'], 2, ',', '.') ?></div>
                            </div>
                            
                            <div class="col-md-2">
                                <label class="form-label small">Quantidade</label>
                                <div class="input-group input-group-sm">
                                    <button type="button" class="btn btn-outline-secondary" 
                                            onclick="atualizarQuantidade(<?= $item['produto_id'] ?>, <?= $item['variacao_id'] ?>, <?= $item['quantidade'] - 1 ?>)">-</button>
                                    <input type="number" class="form-control text-center" 
                                           value="<?= $item['quantidade'] ?>" 
                                           onchange="atualizarQuantidade(<?= $item['produto_id'] ?>, <?= $item['variacao_id'] ?>, this.value)"
                                           min="1">
                                    <button type="button" class="btn btn-outline-secondary"
                                            onclick="atualizarQuantidade(<?= $item['produto_id'] ?>, <?= $item['variacao_id'] ?>, <?= $item['quantidade'] + 1 ?>)">+</button>
                                </div>
                            </div>
                            
                            <div class="col-md-2 text-end">
                                <label class="form-label small">Subtotal</label>
                                <div class="fw-bold text-success">R$ <?= number_format($item['preco_total'], 2, ',', '.') ?></div>
                                <button type="button" class="btn btn-sm btn-outline-danger mt-1"
                                        onclick="removerDoCarrinho(<?= $item['produto_id'] ?>, <?= $item['variacao_id'] ?>)">
                                    <i class="fas fa-trash"></i> Remover
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-calculator"></i> Resumo do Pedido</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <span id="subtotal">R$ <?= number_format($subtotal, 2, ',', '.') ?></span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Frete:</span>
                        <span id="frete" class="<?= $frete == 0 ? 'text-success' : '' ?>">
                            <?= $frete == 0 ? 'GRÁTIS' : 'R$ ' . number_format($frete, 2, ',', '.') ?>
                        </span>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between mb-3">
                        <strong>Total:</strong>
                        <strong id="total" class="text-success">R$ <?= number_format($total, 2, ',', '.') ?></strong>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <a href="<?= BASE_URL ?>/?controller=Home&action=checkout" class="btn btn-success btn-lg">
                            <i class="fas fa-credit-card"></i> Finalizar Compra
                        </a>
                        <a href="<?= BASE_URL ?>" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left"></i> Continuar Comprando
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Informações sobre frete -->
            <div class="card mt-3">
                <div class="card-body">
                    <h6><i class="fas fa-info-circle text-info"></i> Informações de Frete</h6>
                    <small class="text-muted">
                        <ul class="mb-0">
                            <li>Frete grátis para compras acima de R$ 200,00</li>
                            <li>Entre R$ 52,00 e R$ 166,59: R$ 15,00</li>
                            <li>Outros valores: R$ 20,00</li>
                        </ul>
                    </small>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php include BASE_PATH . '/views/layout/footer.php'; ?> 