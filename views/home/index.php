<?php include BASE_PATH . '/views/layout/header.php'; ?>

<div class="row mb-4">
    <div class="col-12">
        <h1 class="display-4 text-center mb-4">
            <i class="fas fa-store text-primary"></i>
            Bem-vindo ao MINI ERP
        </h1>
        <p class="lead text-center text-muted">
            Descubra nossos produtos incríveis com os melhores preços!
        </p>
    </div>
</div>

<?php if (empty($produtos)): ?>
    <div class="row">
        <div class="col-12">
            <div class="alert alert-info text-center">
                <h4><i class="fas fa-info-circle"></i> Nenhum produto encontrado</h4>
                <p>Ainda não há produtos cadastrados no sistema.</p>
                <a href="<?= BASE_URL ?>/?controller=Produto&action=create" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Cadastrar Primeiro Produto
                </a>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="row">
        <?php foreach ($produtos as $produto): ?>
            <div class="col-md-4 col-lg-3 mb-4">
                <div class="card product-card h-100 shadow-sm">
                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                        <i class="fas fa-image text-muted" style="font-size: 3rem;"></i>
                    </div>
                    
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?= htmlspecialchars($produto['nome']) ?></h5>
                        <p class="card-text text-muted small flex-grow-1">
                            <?= htmlspecialchars($produto['descricao']) ?>
                        </p>
                        
                        <div class="price mb-3">
                            R$ <?= number_format($produto['preco'], 2, ',', '.') ?>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <a href="<?= BASE_URL ?>/?controller=Home&action=produto&id=<?= $produto['id'] ?>" 
                               class="btn btn-outline-primary">
                                <i class="fas fa-eye"></i> Ver Detalhes
                            </a>
                        </div>
                    </div>
                    
                    <div class="card-footer bg-white border-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="rating">
                                <?php 
                                // Gerar rating aleatório entre 4.0 e 5.0 para produtos "populares"
                                $rating = number_format(rand(40, 50) / 10, 1);
                                $fullStars = floor($rating);
                                $hasHalfStar = ($rating - $fullStars) >= 0.5;
                                ?>
                                <small class="text-warning me-1">
                                    <?php for($i = 1; $i <= 5; $i++): ?>
                                        <?php if($i <= $fullStars): ?>
                                            <i class="fas fa-star"></i>
                                        <?php elseif($i == $fullStars + 1 && $hasHalfStar): ?>
                                            <i class="fas fa-star-half-alt"></i>
                                        <?php else: ?>
                                            <i class="far fa-star"></i>
                                        <?php endif; ?>
                                    <?php endfor; ?>
                                </small>
                                <small class="text-muted"><?= $rating ?></small>
                            </div>
                            <small class="text-success fw-bold">
                                <i class="fas fa-medal"></i> Popular
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Seção de vantagens -->
<div class="row mt-5 pt-5 border-top">
    <div class="col-12">
        <h2 class="text-center mb-4">Por que escolher o Mini ERP?</h2>
    </div>
    
    <div class="col-md-4 text-center mb-4">
        <div class="card border-0">
            <div class="card-body">
                <i class="fas fa-shipping-fast text-primary mb-3" style="font-size: 3rem;"></i>
                <h5>Entrega Rápida</h5>
                <p class="text-muted">Frete grátis para compras acima de R$ 200,00</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 text-center mb-4">
        <div class="card border-0">
            <div class="card-body">
                <i class="fas fa-shield-alt text-success mb-3" style="font-size: 3rem;"></i>
                <h5>Compra Segura</h5>
                <p class="text-muted">Seus dados estão protegidos conosco</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 text-center mb-4">
        <div class="card border-0">
            <div class="card-body">
                <i class="fas fa-headset text-info mb-3" style="font-size: 3rem;"></i>
                <h5>Suporte 24/7</h5>
                <p class="text-muted">Estamos sempre aqui para ajudar você</p>
            </div>
        </div>
    </div>
</div>

<?php include BASE_PATH . '/views/layout/footer.php'; ?> 