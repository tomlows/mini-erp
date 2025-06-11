<?php include BASE_PATH . '/views/layout/header.php'; ?>

<div class="row">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="<?= BASE_URL ?>">Início</a>
                </li>
                <li class="breadcrumb-item active"><?= htmlspecialchars($produto['nome']) ?></li>
            </ol>
        </nav>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body text-center bg-light" style="height: 400px;">
                <div class="d-flex align-items-center justify-content-center h-100">
                    <i class="fas fa-image text-muted" style="font-size: 6rem;"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <h1 class="h2"><?= htmlspecialchars($produto['nome']) ?></h1>
        <p class="text-muted mb-4"><?= htmlspecialchars($produto['descricao']) ?></p>
        
        <div class="price mb-4" style="font-size: 2rem;">
            R$ <?= number_format($produto['preco'], 2, ',', '.') ?>
            <span id="valor-adicional" class="text-muted" style="font-size: 1rem;"></span>
        </div>
        
        <?php if (!empty($produto['variacoes'])): ?>
            <form id="form-comprar">
                <input type="hidden" name="produto_id" value="<?= $produto['id'] ?>">
                
<?php if (count($produto['variacoes']) > 1 || $produto['variacoes'][0]['nome'] !== 'Padrão'): ?>
                <div class="mb-3">
                    <label for="variacao" class="form-label">Escolha a variação:</label>
                    <select name="variacao_id" id="variacao" class="form-select" required>
                        <option value="">Selecione uma opção...</option>
                        <?php foreach ($produto['variacoes'] as $variacao): ?>
                            <option value="<?= $variacao['id'] ?>" 
                                    data-preco-adicional="<?= $variacao['valor_adicional'] ?>"
                                    data-estoque="<?= $variacao['estoque'] ?>"
                                    <?= $variacao['estoque'] <= 0 ? 'disabled' : '' ?>>
                                <?= htmlspecialchars($variacao['nome']) ?>
                                <?php if ($variacao['valor_adicional'] > 0): ?>
                                    (+R$ <?= number_format($variacao['valor_adicional'], 2, ',', '.') ?>)
                                <?php endif; ?>
                                - Estoque: <?= $variacao['estoque'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php else: ?>
                <!-- Variação padrão única - seleção automática -->
                <input type="hidden" name="variacao_id" id="variacao-hidden" value="<?= $produto['variacoes'][0]['id'] ?>">
                <div class="mb-3">
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle"></i>
                        <strong>Produto padrão</strong> - Estoque disponível: <?= $produto['variacoes'][0]['estoque'] ?> unidades
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="mb-3">
                    <label for="quantidade" class="form-label">Quantidade:</label>
                    <div class="input-group" style="max-width: 150px;">
                        <button type="button" class="btn btn-outline-secondary" onclick="alterarQuantidade(-1)">-</button>
                        <input type="number" name="quantidade" id="quantidade" class="form-control text-center" 
                               value="1" min="1" max="1" readonly>
                        <button type="button" class="btn btn-outline-secondary" onclick="alterarQuantidade(1)">+</button>
                    </div>
                    <small class="text-muted">Estoque disponível: <span id="estoque-disponivel">0</span></small>
                </div>
                
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-success btn-lg" disabled>
                        <i class="fas fa-shopping-cart"></i>
                        Adicionar ao Carrinho
                    </button>
                    <a href="<?= BASE_URL ?>/?controller=Home&action=carrinho" class="btn btn-outline-primary">
                        <i class="fas fa-eye"></i>
                        Ver Carrinho
                    </a>
                </div>
            </form>
        <?php else: ?>
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i>
                Este produto não possui variações disponíveis.
            </div>
        <?php endif; ?>
        
        <hr class="my-4">
        
        <div class="row text-center">
            <div class="col-4">
                <i class="fas fa-truck text-primary mb-2"></i>
                <small class="d-block">Entrega rápida</small>
            </div>
            <div class="col-4">
                <i class="fas fa-shield-alt text-success mb-2"></i>
                <small class="d-block">Compra segura</small>
            </div>
            <div class="col-4">
                <i class="fas fa-undo text-info mb-2"></i>
                <small class="d-block">Troca fácil</small>
            </div>
        </div>
    </div>
</div>

<!-- Script movido para o footer -->



<?php include BASE_PATH . '/views/layout/footer.php'; ?> 