<?php include BASE_PATH . '/views/layout/header.php'; ?>

<div class="row">
    <div class="col-12">
        <h1><i class="fas fa-plus"></i> Novo Produto</h1>
        <hr>
    </div>
</div>

<form method="POST" action="<?= BASE_URL ?>/?controller=Produto&action=create">
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
                        <input type="text" class="form-control" name="nome" id="nome" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="preco" class="form-label">Preço Base *</label>
                        <div class="input-group">
                            <span class="input-group-text">R$</span>
                            <input type="number" class="form-control" name="preco" id="preco" 
                                   step="0.01" min="0" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="descricao" class="form-label">Descrição</label>
                        <textarea class="form-control" name="descricao" id="descricao" rows="3"></textarea>
                    </div>
                </div>
            </div>
            
            <!-- Variações -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-list"></i> Variações do Produto</h5>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="adicionarVariacao()">
                        <i class="fas fa-plus"></i> Adicionar Variação
                    </button>
                </div>
                <div class="card-body">
                    <div id="variacoes-container">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            As variações permitem que um produto tenha diferentes opções (tamanho, cor, etc.) com preços e estoques individuais.
                        </div>
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
                            <i class="fas fa-save"></i> Salvar Produto
                        </button>
                        <a href="<?= BASE_URL ?>/?controller=Produto&action=index" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Cancelar
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-body">
                    <h6><i class="fas fa-lightbulb text-warning"></i> Dicas</h6>
                    <ul class="small text-muted mb-0">
                        <li>Use nomes descritivos para as variações</li>
                        <li>O valor adicional será somado ao preço base</li>
                        <li>Defina o estoque inicial para cada variação</li>
                        <li>Você pode editar tudo depois</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
let variacaoCount = 0;

function adicionarVariacao() {
    variacaoCount++;
    const container = document.getElementById('variacoes-container');
    
    const variacaoHtml = `
        <div class="variacao-item border rounded p-3 mb-3" id="variacao-${variacaoCount}">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0">Variação #${variacaoCount}</h6>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removerVariacao(${variacaoCount})">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Nome da Variação *</label>
                        <input type="text" class="form-control" name="variacoes[${variacaoCount}][nome]" 
                               placeholder="Ex: P, M, G, Azul, etc." required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label class="form-label">Valor Adicional</label>
                        <div class="input-group">
                            <span class="input-group-text">R$</span>
                            <input type="number" class="form-control" name="variacoes[${variacaoCount}][valor_adicional]" 
                                   step="0.01" value="0" min="0">
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label class="form-label">Estoque Inicial</label>
                        <input type="number" class="form-control" name="variacoes[${variacaoCount}][estoque]" 
                               value="0" min="0">
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remove alert se existir
    const alert = container.querySelector('.alert');
    if (alert) alert.remove();
    
    container.insertAdjacentHTML('beforeend', variacaoHtml);
}

function removerVariacao(id) {
    const variacao = document.getElementById(`variacao-${id}`);
    if (variacao) {
        variacao.remove();
        
        // Adiciona alert se não houver mais variações
        const container = document.getElementById('variacoes-container');
        if (container.children.length === 0) {
            container.innerHTML = `
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    As variações permitem que um produto tenha diferentes opções (tamanho, cor, etc.) com preços e estoques individuais.
                </div>
            `;
        }
    }
}

// Adicionar primeira variação automaticamente
document.addEventListener('DOMContentLoaded', function() {
    adicionarVariacao();
});
</script>

<?php include BASE_PATH . '/views/layout/footer.php'; ?> 