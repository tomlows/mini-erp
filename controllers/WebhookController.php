<?php
class WebhookController extends Controller {
    
    public function pedidoStatus() {
        // Log detalhado do webhook
        $timestamp = date('Y-m-d H:i:s');
        error_log("=== WEBHOOK CHAMADO - $timestamp ===");
        error_log("🌐 Método: " . $_SERVER['REQUEST_METHOD']);
        error_log("🌐 User-Agent: " . ($_SERVER['HTTP_USER_AGENT'] ?? 'Não informado'));
        error_log("🌐 IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'Não informado'));
        
        try {
            // Validar método HTTP
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                error_log("❌ Método incorreto: " . $_SERVER['REQUEST_METHOD']);
                http_response_code(405);
                $this->json([
                    'error' => 'Método não permitido. Use POST.',
                    'allowed_methods' => ['POST'],
                    'received_method' => $_SERVER['REQUEST_METHOD']
                ]);
                return;
            }
            
            // Receber dados do webhook
            $rawInput = file_get_contents('php://input');
            error_log("📦 Raw input: " . $rawInput);
            
            $data = json_decode($rawInput, true);
            error_log("📦 JSON decodificado: " . print_r($data, true));
            
            // Se não conseguiu decodificar JSON, tentar $_POST
            if (!$data) {
                $data = $_POST;
                error_log("📦 Usando $_POST: " . print_r($data, true));
            }
            
            // Validar dados obrigatórios
            $pedidoId = $data['pedido_id'] ?? $data['id'] ?? null;
            $status = $data['status'] ?? null;
            
            error_log("🔍 ID do pedido extraído: $pedidoId");
            error_log("🔍 Status extraído: $status");
            
            if (!$pedidoId || !$status) {
                error_log("❌ Dados obrigatórios faltando");
                http_response_code(400);
                $this->json([
                    'success' => false,
                    'error' => 'Dados obrigatórios faltando',
                    'required_fields' => [
                        'pedido_id' => 'ID do pedido (número)',
                        'status' => 'Status do pedido (string)'
                    ],
                    'received_data' => $data,
                    'example' => [
                        'pedido_id' => 123,
                        'status' => 'confirmado'
                    ]
                ]);
                return;
            }
            
            // Validar status
            $validStatuses = ['pendente', 'confirmado', 'processando', 'enviado', 'entregue', 'cancelado'];
            $status = strtolower(trim($status));
            
            if (!in_array($status, $validStatuses)) {
                error_log("❌ Status inválido: $status");
                http_response_code(400);
                $this->json([
                    'success' => false,
                    'error' => 'Status inválido',
                    'received_status' => $status,
                    'valid_statuses' => $validStatuses
                ]);
                return;
            }
            
            $pedidoModel = new Pedido();
            
            // Verificar se o pedido existe
            $pedido = $pedidoModel->getById($pedidoId);
            if (!$pedido) {
                error_log("❌ Pedido não encontrado: $pedidoId");
                http_response_code(404);
                $this->json([
                    'success' => false,
                    'error' => 'Pedido não encontrado',
                    'pedido_id' => $pedidoId
                ]);
                return;
            }
            
            error_log("✅ Pedido encontrado - ID: $pedidoId, Status atual: {$pedido['status']}");
            
            // Processar mudança de status
            $statusAnterior = $pedido['status'];
            
            if ($status === 'cancelado') {
                // Status cancelado: cancelar e remover o pedido
                error_log("🗑️ Iniciando cancelamento do pedido $pedidoId");
                
                // Verificar se já não está cancelado
                if ($statusAnterior === 'cancelado') {
                    error_log("⚠️ Pedido $pedidoId já estava cancelado");
                    $this->json([
                        'success' => true,
                        'message' => 'Pedido já estava cancelado',
                        'pedido_id' => $pedidoId,
                        'status_anterior' => $statusAnterior,
                        'action' => 'no_change'
                    ]);
                    return;
                }
                
                $resultado = $pedidoModel->cancelar($pedidoId);
                
                if ($resultado) {
                    error_log("✅ Pedido $pedidoId cancelado e removido com sucesso");
                    $this->json([
                        'success' => true,
                        'message' => 'Pedido cancelado e removido com sucesso',
                        'pedido_id' => $pedidoId,
                        'status_anterior' => $statusAnterior,
                        'action' => 'cancelled_and_removed',
                        'estoque_restaurado' => true
                    ]);
                } else {
                    error_log("❌ Erro ao cancelar pedido $pedidoId");
                    http_response_code(500);
                    $this->json([
                        'success' => false,
                        'error' => 'Erro interno ao cancelar pedido',
                        'pedido_id' => $pedidoId
                    ]);
                }
            } else {
                // Outros status: atualizar o status do pedido
                error_log("📝 Atualizando status do pedido $pedidoId de '$statusAnterior' para '$status'");
                
                // Verificar se é realmente uma mudança
                if ($statusAnterior === $status) {
                    error_log("⚠️ Status do pedido $pedidoId já era '$status'");
                    $this->json([
                        'success' => true,
                        'message' => 'Status já estava definido como solicitado',
                        'pedido_id' => $pedidoId,
                        'current_status' => $status,
                        'action' => 'no_change'
                    ]);
                    return;
                }
                
                $resultado = $pedidoModel->updateStatus($pedidoId, $status);
                
                if ($resultado) {
                    error_log("✅ Status do pedido $pedidoId atualizado com sucesso: '$statusAnterior' → '$status'");
                    $this->json([
                        'success' => true,
                        'message' => 'Status do pedido atualizado com sucesso',
                        'pedido_id' => $pedidoId,
                        'status_anterior' => $statusAnterior,
                        'status_novo' => $status,
                        'action' => 'status_updated'
                    ]);
                } else {
                    error_log("❌ Erro ao atualizar status do pedido $pedidoId");
                    http_response_code(500);
                    $this->json([
                        'success' => false,
                        'error' => 'Erro interno ao atualizar status do pedido',
                        'pedido_id' => $pedidoId
                    ]);
                }
            }
            
        } catch (Exception $e) {
            error_log("💥 Erro crítico no webhook: " . $e->getMessage());
            error_log("📍 Stack trace: " . $e->getTraceAsString());
            
            http_response_code(500);
            $this->json([
                'success' => false,
                'error' => 'Erro interno do servidor',
                'message' => $e->getMessage(),
                'timestamp' => date('Y-m-d H:i:s')
            ]);
        }
        
        error_log("=== FIM DO WEBHOOK ===\n");
    }
    
    public function teste() {
        // Página de teste do webhook
        $pedidoModel = new Pedido();
        $pedidos = $pedidoModel->getAll();
        
        $this->render('webhook/test', [
            'pedidos' => $pedidos
        ]);
    }
    
    public function documentacao() {
        // Documentação completa do webhook
        $this->render('webhook/docs');
    }
    
    public function healthCheck() {
        // Endpoint para verificar se o webhook está funcionando
        $this->json([
            'status' => 'ok',
            'service' => 'webhook_pedido_status',
            'timestamp' => date('Y-m-d H:i:s'),
            'version' => '1.0',
            'endpoints' => [
                'pedido_status' => BASE_URL . '/?controller=Webhook&action=pedidoStatus',
                'teste' => BASE_URL . '/?controller=Webhook&action=teste',
                'docs' => BASE_URL . '/?controller=Webhook&action=documentacao'
            ]
        ]);
    }
}
?> 