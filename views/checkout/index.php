<?php include BASE_PATH . '/views/layout/header.php'; ?>

<div class="row">
    <div class="col-12">
        <h1><i class="fas fa-credit-card"></i> Finalizar Compra</h1>
        <hr>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <form id="form-checkout">
            <!-- Dados do Cliente -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-user"></i> Dados do Cliente</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nome" class="form-label">Nome completo *</label>
                                <input type="text" class="form-control" name="nome" id="nome" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">E-mail *</label>
                                <input type="email" class="form-control" name="email" id="email" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="telefone" class="form-label">Telefone</label>
                                <input type="tel" class="form-control" name="telefone" id="telefone">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Endereço de Entrega -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-map-marker-alt"></i> Endereço de Entrega</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="cep" class="form-label">CEP *</label>
                                <input type="text" class="form-control" name="cep" id="cep" 
                                       placeholder="00000-000" required maxlength="9">
                                <div class="form-text">Digite o CEP para buscar automaticamente</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="endereco" class="form-label">Endereço *</label>
                                <input type="text" class="form-control" name="endereco" id="endereco" required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label for="numero" class="form-label">Número *</label>
                                <input type="text" class="form-control" name="numero" id="numero" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="complemento" class="form-label">Complemento</label>
                                <input type="text" class="form-control" name="complemento" id="complemento">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="bairro" class="form-label">Bairro *</label>
                                <input type="text" class="form-control" name="bairro" id="bairro" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="cidade" class="form-label">Cidade *</label>
                                <input type="text" class="form-control" name="cidade" id="cidade" required>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="mb-3">
                                <label for="uf" class="form-label">UF *</label>
                                <input type="text" class="form-control" name="uf" id="uf" 
                                       required maxlength="2" style="text-transform: uppercase;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Cupom de Desconto -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-ticket-alt"></i> Cupom de Desconto</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-group">
                                <input type="text" class="form-control" name="cupom_codigo" id="cupom_codigo" 
                                       placeholder="Digite o código do cupom">
                                <button type="button" class="btn btn-outline-primary" onclick="validarCupom()">
                                    Aplicar
                                </button>
                            </div>
                        </div>
                    </div>
                    <div id="cupom-resultado" class="mt-2"></div>
                </div>
            </div>
        </form>
    </div>
    
    <!-- Resumo do Pedido -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-shopping-cart"></i> Resumo do Pedido</h5>
            </div>
            <div class="card-body">
                <!-- Itens do Carrinho -->
                <div class="mb-3">
                    <?php foreach ($itens as $item): ?>
                        <div class="d-flex justify-content-between mb-2">
                            <div>
                                <small class="fw-bold"><?= htmlspecialchars($item['produto_nome']) ?></small>
                                <?php if ($item['variacao_nome']): ?>
                                    <br><small class="text-muted"><?= htmlspecialchars($item['variacao_nome']) ?></small>
                                <?php endif; ?>
                                <br><small class="text-muted">Qtd: <?= $item['quantidade'] ?></small>
                            </div>
                            <div class="text-end">
                                <small>R$ <?= number_format($item['preco_total'], 2, ',', '.') ?></small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <hr>
                
                <!-- Valores -->
                <div class="d-flex justify-content-between mb-2">
                    <span>Subtotal:</span>
                    <span id="checkout-subtotal">R$ <?= number_format($subtotal, 2, ',', '.') ?></span>
                </div>
                
                <div class="d-flex justify-content-between mb-2" id="desconto-linha" style="display: none !important;">
                    <span class="text-success">Desconto:</span>
                    <span id="checkout-desconto" class="text-success">-R$ 0,00</span>
                </div>
                
                <div class="d-flex justify-content-between mb-2">
                    <span>Frete:</span>
                    <span id="checkout-frete" class="<?= $frete == 0 ? 'text-success' : '' ?>">
                        <?= $frete == 0 ? 'GRÁTIS' : 'R$ ' . number_format($frete, 2, ',', '.') ?>
                    </span>
                </div>
                
                <hr>
                
                <div class="d-flex justify-content-between mb-3">
                    <strong>Total:</strong>
                    <strong id="checkout-total" class="text-success">R$ <?= number_format($total, 2, ',', '.') ?></strong>
                </div>
                
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-success btn-lg" onclick="finalizarPedido()">
                        <i class="fas fa-check"></i> Confirmar Pedido
                    </button>
                    <a href="<?= BASE_URL ?>/?controller=Home&action=carrinho" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Voltar ao Carrinho
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Segurança -->
        <div class="card mt-3">
            <div class="card-body text-center">
                <i class="fas fa-shield-alt text-success fa-2x mb-2"></i>
                <h6>Compra 100% Segura</h6>
                <small class="text-muted">Seus dados estão protegidos</small>
            </div>
        </div>
    </div>
</div>

<script>
let cupomAplicado = false;
let descontoAtual = 0;

function validarCupom() {
    const codigo = $('#cupom_codigo').val().trim();
    
    if (!codigo) {
        showAlert('warning', 'Digite o código do cupom');
        return;
    }
    
    $.ajax({
        url: '/mini_erp/?controller=Pedido&action=validarCupom',
        method: 'POST',
        data: { codigo: codigo },
        dataType: 'json'
    })
    .done(function(response) {
        if (response && response.success) {
            cupomAplicado = true;
            descontoAtual = response.desconto;
            
            $('#cupom-resultado').html(`
                <div class="alert alert-success">
                    <i class="fas fa-check"></i> ${response.message}
                    <br>Desconto: R$ ${response.desconto.toFixed(2).replace('.', ',')}
                </div>
            `);
            
            // Atualizar valores
            $('#desconto-linha').show();
            $('#checkout-desconto').text('-R$ ' + response.desconto.toFixed(2).replace('.', ','));
            $('#checkout-frete').text(response.frete == 0 ? 'GRÁTIS' : 'R$ ' + response.frete.toFixed(2).replace('.', ','));
            $('#checkout-total').text('R$ ' + response.total.toFixed(2).replace('.', ','));
            
            showAlert('success', response.message);
        } else {
            cupomAplicado = false;
            descontoAtual = 0;
            $('#cupom-resultado').html(`
                <div class="alert alert-danger">
                    <i class="fas fa-times"></i> ${response.message || 'Cupom inválido'}
                </div>
            `);
        }
    })
    .fail(function(xhr, status, error) {
        console.error('Erro ao validar cupom:', {xhr, status, error});
        showAlert('danger', 'Erro ao validar cupom: ' + error);
    });
}

function finalizarPedido() {
    const form = $('#form-checkout')[0];
    
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    const formData = new FormData(form);
    
    if (cupomAplicado) {
        formData.append('cupom_codigo', $('#cupom_codigo').val());
    }
    
    $.ajax({
        url: '/mini_erp/?controller=Pedido&action=finalizar',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json'
    })
    .done(function(response) {
        if (response && response.success) {
            showAlert('success', response.message);
            setTimeout(function() {
                window.location.href = '/mini_erp/?controller=Pedido&action=detalhes&id=' + response.pedido_id;
            }, 2000);
        } else {
            showAlert('danger', response.message || 'Erro ao finalizar pedido');
        }
    })
    .fail(function(xhr, status, error) {
        console.error('Erro ao finalizar pedido:', {xhr, status, error});
        showAlert('danger', 'Erro ao finalizar pedido: ' + error);
    });
}
</script>

<?php include BASE_PATH . '/views/layout/footer.php'; ?> 