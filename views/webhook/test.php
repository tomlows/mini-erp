<?php 
include BASE_PATH . '/views/layout/header.php'; 

// Buscar pedidos recentes para facilitar os testes
$pedidoModel = new Pedido();
$pedidosRecentes = $pedidoModel->getRecentes(10);
?>

<div class="row">
    <div class="col-12">
        <h1><i class="fas fa-exchange-alt"></i> Teste do Webhook</h1>
        <p class="text-muted">Teste o webhook de atualização de status de pedidos</p>
        <hr>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-list"></i> Pedidos Disponíveis para Teste</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($pedidosRecentes)): ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Cliente</th>
                                    <th>Status Atual</th>
                                    <th>Total</th>
                                    <th>Data</th>
                                    <th>Ação</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pedidosRecentes as $pedido): ?>
                                <tr>
                                    <td><strong><?= $pedido['id'] ?></strong></td>
                                    <td><?= htmlspecialchars($pedido['cliente_nome']) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $pedido['status'] === 'pendente' ? 'warning' : ($pedido['status'] === 'cancelado' ? 'danger' : 'success') ?>">
                                            <?= ucfirst($pedido['status']) ?>
                                        </span>
                                    </td>
                                    <td>R$ <?= number_format($pedido['total'], 2, ',', '.') ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($pedido['created_at'])) ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" onclick="selecionarPedido(<?= $pedido['id'] ?>)">
                                            <i class="fas fa-hand-pointer"></i> Usar
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> 
                        Não há pedidos cadastrados. Faça uma compra primeiro para testar o webhook.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Testar Webhook de Status</h5>
            </div>
            <div class="card-body">
                <form id="webhook-form">
                    <div class="mb-3">
                        <label for="pedido_id" class="form-label">ID do Pedido</label>
                        <input type="number" class="form-control" id="pedido_id" name="pedido_id" required>
                        <div class="form-text">Digite o ID de um pedido existente</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="status" class="form-label">Novo Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="">Selecione um status...</option>
                            <option value="pendente">Pendente</option>
                            <option value="confirmado">Confirmado</option>
                            <option value="processando">Processando</option>
                            <option value="enviado">Enviado</option>
                            <option value="entregue">Entregue</option>
                            <option value="cancelado" class="text-danger">⚠️ Cancelado (REMOVE PEDIDO)</option>
                        </select>
                        <div class="form-text">
                            <small class="text-danger">
                                <i class="fas fa-exclamation-triangle"></i> 
                                <strong>ATENÇÃO:</strong> Status "cancelado" remove o pedido permanentemente!
                            </small>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Enviar Webhook
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="mostrar-curl">
                            <i class="fas fa-terminal"></i> Ver Comando cURL
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Informações do Webhook</h5>
            </div>
            <div class="card-body">
                <h6>Endpoint:</h6>
                <code>http://localhost/mini_erp/?controller=Webhook&action=pedidoStatus</code>
                
                <h6 class="mt-3">Método:</h6>
                <span class="badge bg-success">POST</span>
                
                <h6 class="mt-3">Payload (JSON):</h6>
                <pre class="bg-light p-2"><code>{
  "pedido_id": 123,
  "status": "confirmado"
}</code></pre>
                
                <h6 class="mt-3">Comportamento:</h6>
                <ul class="list-unstyled">
                    <li><i class="fas fa-check text-success"></i> <strong>Status = "cancelado":</strong> Remove o pedido e devolve estoque</li>
                    <li><i class="fas fa-edit text-primary"></i> <strong>Outros status:</strong> Atualiza o status do pedido</li>
                </ul>
                
                <h6 class="mt-3">Códigos de Resposta:</h6>
                <ul class="list-unstyled">
                    <li><span class="badge bg-success">200</span> Sucesso</li>
                    <li><span class="badge bg-warning">400</span> Dados inválidos</li>
                    <li><span class="badge bg-danger">404</span> Pedido não encontrado</li>
                    <li><span class="badge bg-secondary">405</span> Método não permitido</li>
                    <li><span class="badge bg-danger">500</span> Erro interno</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Resultado do Teste</h5>
            </div>
            <div class="card-body">
                <div id="resultado" class="d-none">
                    <h6>Resposta:</h6>
                    <pre id="resposta-json" class="bg-light p-3"></pre>
                    
                    <h6>Status HTTP:</h6>
                    <span id="status-http" class="badge"></span>
                </div>
                
                <div id="aguardando" class="text-muted">
                    <i class="fas fa-info-circle"></i> Preencha o formulário e clique em "Enviar Webhook" para testar
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Função para selecionar um pedido da lista
function selecionarPedido(id) {
    // Aguardar jQuery estar disponível
    if (typeof $ === 'undefined') {
        setTimeout(() => selecionarPedido(id), 100);
        return;
    }
    
    console.log('🎯 Pedido selecionado:', id);
    
    // Preencher o campo ID
    $('#pedido_id').val(id);
    
    // Atualizar o card de resultado para mostrar que o pedido foi selecionado
    $('#resultado').addClass('d-none');
    $('#aguardando').removeClass('d-none').html(`
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> 
            <strong>Pedido #${id} selecionado!</strong><br>
            <small>Escolha o novo status e clique em "Enviar Webhook" para testar.</small>
        </div>
    `);
    
    // Destacar o campo status para o próximo passo
    $('#status').focus();
    
    // Mostrar alerta de sucesso
    showAlert('info', 'Pedido #' + id + ' selecionado. Escolha o novo status e envie o webhook.');
    
    // Scroll para o formulário
    $('html, body').animate({
        scrollTop: $("#webhook-form").offset().top - 100
    }, 500);
}

// Aguardar jQuery e DOM estarem prontos
function initWebhookTest() {
    // Verificar se jQuery está disponível
    if (typeof $ === 'undefined') {
        console.log('⏳ Aguardando jQuery carregar...');
        setTimeout(initWebhookTest, 100);
        return;
    }
    console.log('🔗 Página de teste do webhook carregada');
    
    // Verificar se jQuery e showAlert estão disponíveis
    if (typeof showAlert !== 'function') {
        console.error('❌ showAlert não encontrada');
        window.showAlert = function(type, message) {
            alert(type.toUpperCase() + ': ' + message);
        };
    } else {
        console.log('✅ showAlert encontrada');
    }
    
    $('#webhook-form').on('submit', function(e) {
        e.preventDefault();
        console.log('🚀 Formulário de webhook submetido');
        
        const pedidoId = $('#pedido_id').val();
        const status = $('#status').val();
        
        console.log('📊 Dados do formulário:', {pedidoId, status});
        
        if (!pedidoId || !status) {
            console.warn('⚠️ Campos obrigatórios faltando');
            showAlert('warning', 'Preencha todos os campos');
            return;
        }
        
        // Mostrar loading no card de resultado
        $('#resultado').addClass('d-none');
        $('#aguardando').removeClass('d-none').html(`
            <div class="alert alert-info">
                <i class="fas fa-spinner fa-spin me-2"></i>
                <strong>Enviando webhook para pedido #${pedidoId}...</strong><br>
                <small>Aguarde enquanto processamos a solicitação.</small>
            </div>
        `);
        
        // Log do que está sendo enviado
        const payload = {
            pedido_id: parseInt(pedidoId),
            status: status
        };
        console.log('📤 Payload do webhook:', payload);
        console.log('🌐 URL destino: /mini_erp/?controller=Webhook&action=pedidoStatus');
        
        $.ajax({
            url: '/mini_erp/?controller=Webhook&action=pedidoStatus',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(payload),
            success: function(response, textStatus, xhr) {
                console.log('✅ Webhook executado com sucesso!');
                console.log('📊 Response:', response);
                console.log('🔢 Status Code:', xhr.status);
                
                mostrarResultado(xhr.status, response);
                
                if (status === 'cancelado') {
                    showAlert('success', 'Webhook executado! Pedido foi cancelado e removido.');
                    // Mostrar botão para recarregar ao invés de recarregar automaticamente
                    mostrarBotaoRecarregar('Pedido cancelado - clique para atualizar a lista de pedidos');
                } else {
                    showAlert('success', 'Webhook executado! Status do pedido atualizado para: ' + status);
                    // Mostrar botão para recarregar ao invés de recarregar automaticamente  
                    mostrarBotaoRecarregar('Status atualizado - clique para ver as mudanças na lista');
                }
            },
            error: function(xhr, textStatus, errorThrown) {
                console.error('❌ Erro no webhook!');
                console.error('📊 XHR:', xhr);
                console.error('📝 Status:', textStatus);
                console.error('💥 Error:', errorThrown);
                console.error('📄 Response Text:', xhr.responseText);
                
                const response = xhr.responseJSON || {error: xhr.responseText || errorThrown};
                mostrarResultado(xhr.status, response);
                showAlert('danger', 'Erro no webhook: ' + (response.error || errorThrown));
            }
        });
    });
    
    function mostrarResultado(statusCode, response) {
        $('#aguardando').addClass('d-none');
        $('#resultado').removeClass('d-none');
        
        $('#resposta-json').text(JSON.stringify(response, null, 2));
        
        const badge = $('#status-http');
        badge.text(statusCode);
        
        if (statusCode >= 200 && statusCode < 300) {
            badge.attr('class', 'badge bg-success');
        } else if (statusCode >= 400 && statusCode < 500) {
            badge.attr('class', 'badge bg-warning');
        } else {
            badge.attr('class', 'badge bg-danger');
        }
        
        // Destacar visualmente o resultado com animação
        $('#resultado').addClass('border border-primary').css('transition', 'all 0.3s ease');
        
        // Scroll automático para o resultado após um pequeno delay
        setTimeout(() => {
            $('html, body').animate({
                scrollTop: $("#resultado").offset().top - 100
            }, 800);
            
            // Remover o destaque após alguns segundos
            setTimeout(() => {
                $('#resultado').removeClass('border border-primary');
            }, 3000);
        }, 300);
    }
    
    function mostrarBotaoRecarregar(mensagem) {
        // Adicionar botão de recarregar no final do card resultado
        const botaoHtml = `
            <div class="mt-3 p-3 bg-light rounded" id="area-recarregar">
                <div class="mb-2">
                    <small class="text-success">
                        <i class="fas fa-check-circle me-1"></i>
                        <strong>Resultado permanente!</strong> O resultado ficará visível até você decidir atualizar.
                    </small>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <i class="fas fa-info-circle text-primary me-2"></i>
                        <span class="text-muted">${mensagem}</span>
                    </div>
                    <div>
                        <button class="btn btn-outline-secondary btn-sm me-2" onclick="$('#webhook-form')[0].reset(); resetarResultado();">
                            <i class="fas fa-redo me-1"></i> Novo Teste
                        </button>
                        <button class="btn btn-outline-primary btn-sm" onclick="location.reload()">
                            <i class="fas fa-sync-alt me-1"></i> Atualizar Lista
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        // Remover botão anterior se existir
        $('#area-recarregar').remove();
        
        // Adicionar o novo botão
        $('#resultado').append(botaoHtml);
    }
    
    // Função para resetar o card de resultado
    function resetarResultado() {
        $('#resultado').addClass('d-none');
        $('#area-recarregar').remove(); // Limpar botão de recarregar se existir
        $('#aguardando').removeClass('d-none').html(`
            <i class="fas fa-info-circle"></i> Preencha o formulário e clique em "Enviar Webhook" para testar
        `);
    }
    
    // Resetar resultado quando limpar o formulário
    $('#pedido_id, #status').on('input change', function() {
        const pedidoId = $('#pedido_id').val();
        const status = $('#status').val();
        
        // Se ambos os campos estão vazios, resetar o card
        if (!pedidoId && !status) {
            resetarResultado();
        }
    });
    
    // Exemplo de uso via linha de comando (curl)
    $('#mostrar-curl').on('click', function() {
        const pedidoId = $('#pedido_id').val() || '1';
        const status = $('#status').val() || 'confirmado';
        
        const curlCommand = `curl -X POST "http://localhost/mini_erp/?controller=Webhook&action=pedidoStatus" \\
  -H "Content-Type: application/json" \\
  -d '{"pedido_id": ${pedidoId}, "status": "${status}"}'`;
        
        $('#curl-command').text(curlCommand);
        $('#curl-modal').modal('show');
    });
    
    // Tornar função resetarResultado global para uso externo se necessário
    window.resetarResultado = resetarResultado;
}

// Inicializar quando a página carregar
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initWebhookTest);
} else {
    initWebhookTest();
}
</script>

<!-- Modal para mostrar comando cURL -->
<div class="modal fade" id="curl-modal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-terminal"></i> Comando cURL</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Use este comando no terminal para testar o webhook externamente:</p>
                <pre id="curl-command" class="bg-dark text-light p-3" style="border-radius: 8px;"></pre>
                <button class="btn btn-sm btn-outline-primary" onclick="copiarCurl()">
                    <i class="fas fa-copy"></i> Copiar Comando
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function copiarCurl() {
    const comando = document.getElementById('curl-command').textContent;
    navigator.clipboard.writeText(comando).then(function() {
        showAlert('success', 'Comando cURL copiado para a área de transferência!');
    });
}
</script>

<?php include BASE_PATH . '/views/layout/footer.php'; ?> 