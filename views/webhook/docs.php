<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="display-6">📚 Documentação do Webhook</h1>
                <div>
                    <a href="/?controller=Webhook&action=teste" class="btn btn-primary">🧪 Testar Webhook</a>
                    <a href="/?controller=Webhook&action=healthCheck" class="btn btn-success" target="_blank">✅ Health Check</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Visão Geral -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">🎯 Visão Geral</h5>
                </div>
                <div class="card-body">
                    <p class="lead">O webhook de status permite que sistemas externos atualizem o status de pedidos no Mini ERP ou cancelem pedidos completamente.</p>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h6>🔧 Funcionalidades:</h6>
                            <ul>
                                <li>Atualizar status de pedidos</li>
                                <li>Cancelar pedidos (remove e devolve estoque)</li>
                                <li>Validação robusta de dados</li>
                                <li>Logs detalhados</li>
                                <li>Respostas em JSON</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6>🎫 Status Válidos:</h6>
                            <ul>
                                <li><span class="badge bg-secondary">pendente</span></li>
                                <li><span class="badge bg-info">confirmado</span></li>
                                <li><span class="badge bg-warning">processando</span></li>
                                <li><span class="badge bg-primary">enviado</span></li>
                                <li><span class="badge bg-success">entregue</span></li>
                                <li><span class="badge bg-danger">cancelado</span> (remove pedido)</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Endpoint -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">🌐 Endpoint</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6>URL:</h6>
                        <div class="bg-light p-2 rounded">
                            <code>http://localhost/mini_erp/?controller=Webhook&action=pedidoStatus</code>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <h6>Método:</h6>
                            <span class="badge bg-success fs-6">POST</span>
                        </div>
                        <div class="col-md-4">
                            <h6>Content-Type:</h6>
                            <code>application/json</code>
                        </div>
                        <div class="col-md-4">
                            <h6>Response:</h6>
                            <code>application/json</code>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Request -->
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-warning">
                    <h5 class="mb-0">📤 Request</h5>
                </div>
                <div class="card-body">
                    <h6>Campos Obrigatórios:</h6>
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Campo</th>
                                <th>Tipo</th>
                                <th>Descrição</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><code>pedido_id</code></td>
                                <td>number</td>
                                <td>ID do pedido</td>
                            </tr>
                            <tr>
                                <td><code>status</code></td>
                                <td>string</td>
                                <td>Novo status</td>
                            </tr>
                        </tbody>
                    </table>

                    <h6 class="mt-3">Exemplo de Payload:</h6>
                    <pre class="bg-light p-2"><code>{
  "pedido_id": 123,
  "status": "confirmado"
}</code></pre>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">📥 Response</h5>
                </div>
                <div class="card-body">
                    <h6>Sucesso (200):</h6>
                    <pre class="bg-light p-2"><code>{
  "success": true,
  "message": "Status atualizado com sucesso",
  "pedido_id": 123,
  "status_anterior": "pendente",
  "status_novo": "confirmado",
  "action": "status_updated"
}</code></pre>

                    <h6 class="mt-3">Erro (400/404/500):</h6>
                    <pre class="bg-light p-2"><code>{
  "success": false,
  "error": "Pedido não encontrado",
  "pedido_id": 999
}</code></pre>
                </div>
            </div>
        </div>
    </div>

    <!-- Exemplos cURL -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">💻 Exemplos de Uso</h5>
                </div>
                <div class="card-body">
                    <h6>Atualizar Status:</h6>
                    <pre class="bg-dark text-light p-3"><code>curl -X POST "http://localhost/mini_erp/?controller=Webhook&action=pedidoStatus" \
  -H "Content-Type: application/json" \
  -d '{"pedido_id": 123, "status": "confirmado"}'</code></pre>

                    <h6 class="mt-3">Cancelar Pedido:</h6>
                    <pre class="bg-dark text-light p-3"><code>curl -X POST "http://localhost/mini_erp/?controller=Webhook&action=pedidoStatus" \
  -H "Content-Type: application/json" \
  -d '{"pedido_id": 123, "status": "cancelado"}'</code></pre>
                </div>
            </div>
        </div>
    </div>

    <!-- Códigos de Status -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">📊 Códigos de Status HTTP</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Significado</th>
                                <th>Descrição</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="table-success">
                                <td><strong>200</strong></td>
                                <td>OK</td>
                                <td>Operação realizada com sucesso</td>
                            </tr>
                            <tr class="table-warning">
                                <td><strong>400</strong></td>
                                <td>Bad Request</td>
                                <td>Dados inválidos ou campos obrigatórios faltando</td>
                            </tr>
                            <tr class="table-warning">
                                <td><strong>404</strong></td>
                                <td>Not Found</td>
                                <td>Pedido não encontrado</td>
                            </tr>
                            <tr class="table-warning">
                                <td><strong>405</strong></td>
                                <td>Method Not Allowed</td>
                                <td>Método HTTP incorreto (use POST)</td>
                            </tr>
                            <tr class="table-danger">
                                <td><strong>500</strong></td>
                                <td>Internal Server Error</td>
                                <td>Erro interno do servidor</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Comportamento Especial -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-danger">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">⚠️ ATENÇÃO: Status "cancelado"</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <h6><i class="fas fa-exclamation-triangle"></i> COMPORTAMENTO ESPECIAL:</h6>
                        <p class="mb-0">Quando o status é <code>"cancelado"</code>, o pedido é <strong>REMOVIDO PERMANENTEMENTE</strong> e o estoque é restaurado!</p>
                    </div>

                    <h6>O que acontece no cancelamento:</h6>
                    <ol>
                        <li>✅ Estoque devolvido para todas as variações</li>
                        <li>✅ Uso do cupom revertido (se aplicado)</li>
                        <li>✅ Itens do pedido removidos</li>
                        <li>✅ Pedido removido da base de dados</li>
                        <li>✅ Logs detalhados gerados</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Testes -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">🧪 Testando o Webhook</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Teste via Interface:</h6>
                            <p>Use nossa interface de teste para enviar webhooks facilmente.</p>
                            <a href="/?controller=Webhook&action=teste" class="btn btn-primary">
                                <i class="fas fa-flask"></i> Ir para Teste
                            </a>
                        </div>
                        <div class="col-md-6">
                            <h6>Health Check:</h6>
                            <p>Verifique se o webhook está funcionando corretamente.</p>
                            <a href="/?controller=Webhook&action=healthCheck" class="btn btn-success" target="_blank">
                                <i class="fas fa-heartbeat"></i> Health Check
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Copiar código ao clicar
document.querySelectorAll('pre code').forEach((block) => {
    block.addEventListener('click', () => {
        navigator.clipboard.writeText(block.textContent).then(() => {
            const original = block.style.backgroundColor;
            block.style.backgroundColor = '#28a745';
            setTimeout(() => {
                block.style.backgroundColor = original;
            }, 200);
        });
    });
    
    block.title = 'Clique para copiar';
    block.style.cursor = 'pointer';
});
</script> 