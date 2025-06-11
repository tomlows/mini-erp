<?php include BASE_PATH . '/views/layout/header.php'; ?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-cog"></i> Administração de Produtos</h1>
            <a href="<?= BASE_URL ?>/?controller=Produto&action=create" class="btn btn-primary">
                <i class="fas fa-plus"></i> Novo Produto
            </a>
        </div>
    </div>
</div>

<?php if (empty($produtos)): ?>
    <div class="row">
        <div class="col-12">
            <div class="alert alert-info text-center">
                <h4><i class="fas fa-info-circle"></i> Nenhum produto cadastrado</h4>
                <p>Comece criando seu primeiro produto!</p>
                <a href="<?= BASE_URL ?>/?controller=Produto&action=create" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Criar Primeiro Produto
                </a>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Nome</th>
                                    <th>Preço</th>
                                    <th>Estoque Total</th>
                                    <th>Status</th>
                                    <th>Criado em</th>
                                    <th width="180">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($produtos as $produto): ?>
                                    <?php
                                    // Calcular estoque total
                                    $produtoModel = new Produto();
                                    $estoqueTotal = $produtoModel->getEstoque($produto['id']);
                                    ?>
                                    <tr>
                                        <td>#<?= $produto['id'] ?></td>
                                        <td>
                                            <strong><?= htmlspecialchars($produto['nome']) ?></strong>
                                            <br><small class="text-muted"><?= htmlspecialchars(substr($produto['descricao'], 0, 50)) ?>...</small>
                                        </td>
                                        <td>
                                            <span class="fw-bold text-success">
                                                R$ <?= number_format($produto['preco'], 2, ',', '.') ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= $estoqueTotal > 10 ? 'success' : ($estoqueTotal > 0 ? 'warning' : 'danger') ?>">
                                                <?= $estoqueTotal ?> unidades
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= $produto['ativo'] ? 'success' : 'secondary' ?>">
                                                <?= $produto['ativo'] ? 'Ativo' : 'Inativo' ?>
                                            </span>
                                        </td>
                                        <td>
                                            <small><?= date('d/m/Y H:i', strtotime($produto['created_at'])) ?></small>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="<?= BASE_URL ?>/?controller=Home&action=produto&id=<?= $produto['id'] ?>" 
                                                   class="btn btn-outline-info" title="Visualizar">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="<?= BASE_URL ?>/?controller=Produto&action=edit&id=<?= $produto['id'] ?>" 
                                                   class="btn btn-outline-primary" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button onclick="deletarProduto(<?= $produto['id'] ?>, '<?= htmlspecialchars($produto['nome'], ENT_QUOTES) ?>')" 
                                                        class="btn btn-outline-danger" title="Deletar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Card de estatísticas -->
<div class="row mt-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Total de Produtos</h6>
                        <h3 class="mb-0"><?= count($produtos) ?></h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-box fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Produtos Ativos</h6>
                        <h3 class="mb-0"><?= count(array_filter($produtos, fn($p) => $p['ativo'])) ?></h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Estoque Baixo</h6>
                        <h3 class="mb-0">
                            <?php
                            $produtoModel = new Produto();
                            $estoqueBaixo = 0;
                            foreach ($produtos as $produto) {
                                if ($produtoModel->getEstoque($produto['id']) <= 5) {
                                    $estoqueBaixo++;
                                }
                            }
                            echo $estoqueBaixo;
                            ?>
                        </h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-exclamation-triangle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Valor Total</h6>
                        <h3 class="mb-0">
                            R$ <?= number_format(array_sum(array_column($produtos, 'preco')), 2, ',', '.') ?>
                        </h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-dollar-sign fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function deletarProduto(id, nome) {
    const confirmacao = confirm(
        `Tem certeza que deseja deletar o produto "${nome}"?\n\n` +
        `⚠️ ATENÇÃO: Esta ação NÃO pode ser desfeita!\n\n` +
        `• O produto será permanentemente removido\n` +
        `• Todas as variações serão deletadas\n` +
        `• O estoque será zerado\n\n` +
        `Digite OK para confirmar.`
    );
    
    if (!confirmacao) {
        return;
    }
    
    // Confirmação dupla para segurança
    const confirmacaoFinal = confirm(
        `🚨 ÚLTIMA CONFIRMAÇÃO 🚨\n\n` +
        `Deletar definitivamente o produto "${nome}" (ID: ${id})?\n\n` +
        `Esta ação é IRREVERSÍVEL!`
    );
    
    if (!confirmacaoFinal) {
        return;
    }
    
    // Mostrar loading
    const originalText = event.target.innerHTML;
    event.target.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    event.target.disabled = true;
    
    // Fazer requisição AJAX
    $.ajax({
        url: '<?= BASE_URL ?>/?controller=Produto&action=delete',
        method: 'POST',
        data: { id: id },
        dataType: 'json'
    })
    .done(function(response) {
        if (response && response.success) {
            showAlert('success', `Produto "${nome}" deletado com sucesso!`);
            
            // Remover linha da tabela com animação
            $(`button[onclick*="deletarProduto(${id}"]`).closest('tr').fadeOut(300, function() {
                $(this).remove();
                
                // Verificar se não há mais produtos
                if ($('tbody tr').length === 0) {
                    location.reload();
                }
            });
        } else {
            showAlert('danger', response.message || 'Erro ao deletar produto');
            // Restaurar botão
            event.target.innerHTML = originalText;
            event.target.disabled = false;
        }
    })
    .fail(function(xhr, status, error) {
        console.error('Erro ao deletar produto:', {xhr, status, error});
        showAlert('danger', 'Erro ao deletar produto: ' + error);
        
        // Restaurar botão
        event.target.innerHTML = originalText;
        event.target.disabled = false;
    });
}
</script>

<?php include BASE_PATH . '/views/layout/footer.php'; ?> 