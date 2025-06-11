<?php include BASE_PATH . '/views/layout/header.php'; ?>

<div class="row">
    <div class="col-12">
        <h1><i class="fas fa-edit"></i> Editar Produto: <?= htmlspecialchars($produto['nome']) ?></h1>
        <hr>
    </div>
</div>

<form method="POST" action="<?= BASE_URL ?>/?controller=Produto&action=edit&id=<?= $produto['id'] ?>">
    <div class="row">
        <div class="col-lg-8">
            <!-- Dados do Produto -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Informações do Produto</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome do Produto *</label>
                        <input type="text" class="form-control" name="nome" id="nome" 
                               value="<?= htmlspecialchars($produto['nome']) ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="preco" class="form-label">Preço Base *</label>
                        <div class="input-group">
                            <span class="input-group-text">R$</span>
                            <input type="number" class="form-control" name="preco" id="preco" 
                                   step="0.01" min="0" value="<?= $produto['preco'] ?>" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="descricao" class="form-label">Descrição</label>
                        <textarea class="form-control" name="descricao" id="descricao" rows="3"><?= htmlspecialchars($produto['descricao']) ?></textarea>
                    </div>
                </div>
            </div>
            
            <!-- Variações Existentes -->
            <?php if (!empty($produto['variacoes'])): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-list"></i> Variações e Estoque</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Variação</th>
                                    <th>Valor Adicional</th>
                                    <th>Estoque Atual</th>
                                    <th>Novo Estoque</th>
                                    <th width="100">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($produto['variacoes'] as $variacao): ?>
                                <tr>
                                    <td>
                                        <strong><?= htmlspecialchars($variacao['nome']) ?></strong>
                                    </td>
                                    <td>
                                        <?php if ($variacao['valor_adicional'] > 0): ?>
                                            +R$ <?= number_format($variacao['valor_adicional'], 2, ',', '.') ?>
                                        <?php else: ?>
                                            <span class="text-muted">Sem adicional</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= $variacao['estoque'] > 10 ? 'success' : ($variacao['estoque'] > 0 ? 'warning' : 'danger') ?>">
                                            <?= $variacao['estoque'] ?> unidades
                                        </span>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm" 
                                               name="estoque[<?= $variacao['id'] ?>]" 
                                               value="<?= $variacao['estoque'] ?>" 
                                               min="0" style="width: 100px;">
                                    </td>
                                    <td>
                                        <?php if (count($produto['variacoes']) > 1): ?>
                                        <button type="button" 
                                                onclick="deletarVariacao(<?= $variacao['id'] ?>, '<?= htmlspecialchars($variacao['nome'], ENT_QUOTES) ?>', <?= $produto['id'] ?>)" 
                                                class="btn btn-outline-danger btn-sm" 
                                                title="Deletar Variação">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <?php else: ?>
                                        <button type="button" 
                                                class="btn btn-outline-secondary btn-sm" 
                                                disabled
                                                title="Não é possível deletar a única variação">
                                            <i class="fas fa-ban"></i>
                                        </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <?php if (count($produto['variacoes']) <= 1): ?>
                    <div class="alert alert-warning mt-3">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Única Variação:</strong> Este produto possui apenas uma variação. 
                        Não é possível deletá-la, pois todo produto deve ter pelo menos uma variação.
                        Para modificar esta variação, adicione uma nova antes de deletar a atual.
                    </div>
                    <?php else: ?>
                    <div class="alert alert-info mt-3">
                        <i class="fas fa-info-circle"></i>
                        <strong>Atenção:</strong> Altere apenas os valores de estoque que deseja atualizar. 
                        Para adicionar novas variações, use o botão "Adicionar Variação".
                        Use o botão <i class="fas fa-trash text-danger"></i> para deletar variações desnecessárias.
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Adicionar Nova Variação -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-plus"></i> Adicionar Nova Variação</h5>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="toggleNovaVariacao()">
                        <i class="fas fa-plus"></i> Nova Variação
                    </button>
                </div>
                <div class="card-body" id="nova-variacao-form" style="display: none;">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Nome da Variação</label>
                                <input type="text" class="form-control" id="nova-variacao-nome" 
                                       placeholder="Ex: XG, Vermelho, etc.">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Valor Adicional</label>
                                <div class="input-group">
                                    <span class="input-group-text">R$</span>
                                    <input type="number" class="form-control" id="nova-variacao-valor" 
                                           step="0.01" value="0" min="0">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Estoque Inicial</label>
                                <input type="number" class="form-control" id="nova-variacao-estoque" 
                                       value="0" min="0">
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-success btn-sm" onclick="adicionarNovaVariacao()">
                            <i class="fas fa-plus"></i> Adicionar
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="toggleNovaVariacao()">
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-save"></i> Ações</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Salvar Alterações
                        </button>
                        <a href="<?= BASE_URL ?>/?controller=Home&action=produto&id=<?= $produto['id'] ?>" 
                           class="btn btn-outline-info">
                            <i class="fas fa-eye"></i> Visualizar Produto
                        </a>
                        <a href="<?= BASE_URL ?>/?controller=Produto&action=index" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Voltar à Lista
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Estatísticas -->
            <div class="card mt-3">
                <div class="card-body">
                    <h6><i class="fas fa-chart-bar text-info"></i> Estatísticas</h6>
                    <ul class="list-unstyled mb-0">
                        <li><small class="text-muted">ID do Produto:</small> <strong>#<?= $produto['id'] ?></strong></li>
                        <li><small class="text-muted">Criado em:</small> <strong><?= date('d/m/Y H:i', strtotime($produto['created_at'])) ?></strong></li>
                        <li><small class="text-muted">Última atualização:</small> <strong><?= date('d/m/Y H:i', strtotime($produto['updated_at'])) ?></strong></li>
                        <li><small class="text-muted">Total de variações:</small> <strong><?= count($produto['variacoes']) ?></strong></li>
                        <li><small class="text-muted">Estoque total:</small> <strong>
                            <?php
                            $estoqueTotal = 0;
                            foreach ($produto['variacoes'] as $variacao) {
                                $estoqueTotal += $variacao['estoque'];
                            }
                            echo $estoqueTotal;
                            ?> unidades
                        </strong></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
function toggleNovaVariacao() {
    const form = document.getElementById('nova-variacao-form');
    form.style.display = form.style.display === 'none' ? 'block' : 'none';
    
    // Limpar campos quando esconder
    if (form.style.display === 'none') {
        document.getElementById('nova-variacao-nome').value = '';
        document.getElementById('nova-variacao-valor').value = '0';
        document.getElementById('nova-variacao-estoque').value = '0';
    }
}

function adicionarNovaVariacao() {
    const nome = document.getElementById('nova-variacao-nome').value.trim();
    const valor = document.getElementById('nova-variacao-valor').value;
    const estoque = document.getElementById('nova-variacao-estoque').value;
    
    if (!nome) {
        showAlert('warning', 'Nome da variação é obrigatório');
        return;
    }
    
         $.post('/mini_erp/?controller=Produto&action=adicionarVariacao', {
         produto_id: <?= $produto['id'] ?>,
         nome: nome,
         valor_adicional: valor,
         estoque: estoque
     })
    .done(function(response) {
        if (response.success) {
            showAlert('success', response.message || 'Variação adicionada com sucesso!');
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert('danger', response.message || 'Erro ao adicionar variação');
        }
    })
    .fail(function() {
        showAlert('danger', 'Erro de comunicação ao adicionar variação');
    });
}

function deletarVariacao(variacaoId, nomeVariacao, produtoId) {
    // Verificar quantas variações existem
    const totalVariacoes = $('tbody tr').length;
    
    if (totalVariacoes <= 1) {
        showAlert('warning', 'Não é possível deletar a única variação do produto. Um produto deve ter pelo menos uma variação.');
        return;
    }
    
    const confirmacao = confirm(
        `Tem certeza que deseja deletar a variação "${nomeVariacao}"?\n\n` +
        `⚠️ ATENÇÃO: Esta ação NÃO pode ser desfeita!\n\n` +
        `• A variação será permanentemente removida\n` +
        `• O estoque desta variação será zerado\n` +
        `• Produtos devem ter pelo menos uma variação\n\n` +
        `Digite OK para confirmar.`
    );
    
    if (!confirmacao) {
        return;
    }
    
    // Confirmação adicional para variações com estoque
    const estoqueAtual = $(event.target).closest('tr').find('.badge').text().match(/\d+/);
    if (estoqueAtual && parseInt(estoqueAtual[0]) > 0) {
        const confirmacaoEstoque = confirm(
            `🚨 ATENÇÃO: PERDA DE ESTOQUE 🚨\n\n` +
            `Esta variação possui ${estoqueAtual[0]} unidades em estoque.\n` +
            `Deletar a variação irá ZERAR este estoque!\n\n` +
            `Confirma mesmo assim?`
        );
        
        if (!confirmacaoEstoque) {
            return;
        }
    }
    
    // Mostrar loading no botão
    const botao = event.target.closest('button');
    const originalContent = botao.innerHTML;
    botao.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    botao.disabled = true;
    
    // Fazer requisição AJAX
    $.ajax({
        url: '/mini_erp/?controller=Produto&action=deletarVariacao',
        method: 'POST',
        data: { 
            variacao_id: variacaoId,
            produto_id: produtoId
        },
        dataType: 'json'
    })
    .done(function(response) {
        if (response && response.success) {
            showAlert('success', `Variação "${nomeVariacao}" deletada com sucesso!`);
            
            // Remover linha da tabela com animação
            $(botao).closest('tr').fadeOut(300, function() {
                $(this).remove();
                
                // Verificar se não há mais variações
                const totalLinhas = $('tbody tr:visible').length;
                if (totalLinhas === 0) {
                    showAlert('info', 'Recarregando página para atualizar variações...');
                    setTimeout(() => location.reload(), 1500);
                }
            });
        } else {
            showAlert('danger', response.message || 'Erro ao deletar variação');
            // Restaurar botão
            botao.innerHTML = originalContent;
            botao.disabled = false;
        }
    })
    .fail(function(xhr, status, error) {
        console.error('Erro ao deletar variação:', {xhr, status, error});
        showAlert('danger', 'Erro ao deletar variação: ' + error);
        
        // Restaurar botão
        botao.innerHTML = originalContent;
        botao.disabled = false;
    });
}
</script>

<?php include BASE_PATH . '/views/layout/footer.php'; ?> 