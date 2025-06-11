<?php
class ApiController extends Controller {
    
    public function buscarCep() {
        $cep = $_GET['cep'] ?? '';
        
        if (empty($cep)) {
            $this->json(['success' => false, 'message' => 'CEP é obrigatório']);
            return;
        }
        
        // Limpar CEP (remover caracteres não numéricos)
        $cep = preg_replace('/[^0-9]/', '', $cep);
        
        if (strlen($cep) !== 8) {
            $this->json(['success' => false, 'message' => 'CEP deve ter 8 dígitos']);
            return;
        }
        
        // Buscar CEP na API do ViaCEP
        $url = "https://viacep.com.br/ws/$cep/json/";
        
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false
        ]);
        
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        
        if ($response === false || $httpCode !== 200) {
            $this->json(['success' => false, 'message' => 'Erro ao consultar CEP']);
            return;
        }
        
        $data = json_decode($response, true);
        
        if (isset($data['erro'])) {
            $this->json(['success' => false, 'message' => 'CEP não encontrado']);
            return;
        }
        
        $this->json([
            'success' => true,
            'data' => [
                'cep' => $data['cep'],
                'logradouro' => $data['logradouro'],
                'bairro' => $data['bairro'],
                'localidade' => $data['localidade'],
                'uf' => $data['uf']
            ]
        ]);
    }
    
    public function webhook() {
        // Webhook para receber atualizações de status de pedidos
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            $this->json(['error' => 'Método não permitido']);
            return;
        }
        
        // Ler dados do corpo da requisição
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        
        if (!$data) {
            http_response_code(400);
            $this->json(['error' => 'Dados inválidos']);
            return;
        }
        
        $pedidoId = $data['pedido_id'] ?? null;
        $status = $data['status'] ?? null;
        
        if (!$pedidoId || !$status) {
            http_response_code(400);
            $this->json(['error' => 'pedido_id e status são obrigatórios']);
            return;
        }
        
        $pedidoModel = new Pedido();
        
        // Verificar se o pedido existe
        $pedido = $pedidoModel->getById($pedidoId);
        if (!$pedido) {
            http_response_code(404);
            $this->json(['error' => 'Pedido não encontrado']);
            return;
        }
        
        try {
            if ($status === 'cancelado') {
                // Cancelar pedido (remove o pedido e devolve estoque)
                $success = $pedidoModel->cancelar($pedidoId);
                if ($success) {
                    $this->json(['success' => true, 'message' => 'Pedido cancelado com sucesso']);
                } else {
                    http_response_code(500);
                    $this->json(['error' => 'Erro ao cancelar pedido']);
                }
            } else {
                // Atualizar status do pedido
                $validStatuses = ['pendente', 'confirmado', 'enviado', 'entregue'];
                if (!in_array($status, $validStatuses)) {
                    http_response_code(400);
                    $this->json(['error' => 'Status inválido']);
                    return;
                }
                
                $pedidoModel->updateStatus($pedidoId, $status);
                $this->json(['success' => true, 'message' => 'Status atualizado com sucesso']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            $this->json(['error' => 'Erro interno: ' . $e->getMessage()]);
        }
    }
    
    public function calcularFrete() {
        $subtotal = $_GET['subtotal'] ?? 0;
        
        $pedidoModel = new Pedido();
        $frete = $pedidoModel->calcularFrete($subtotal);
        
        $this->json([
            'success' => true,
            'frete' => $frete,
            'total' => $subtotal + $frete
        ]);
    }
    
    public function verificarEstoque() {
        $produtoId = $_GET['produto_id'] ?? null;
        $variacaoId = $_GET['variacao_id'] ?? null;
        
        if (!$produtoId || !$variacaoId) {
            $this->json(['success' => false, 'message' => 'Dados inválidos']);
            return;
        }
        
        $produtoModel = new Produto();
        $estoque = $produtoModel->getEstoque($produtoId, $variacaoId);
        
        $this->json([
            'success' => true,
            'estoque' => $estoque
        ]);
    }
}
?> 