<?php
class CarrinhoController extends Controller {
    
    public function adicionar() {
        try {
            // Log de debug
            error_log("🛒 CarrinhoController::adicionar() chamado");
            error_log("📊 REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD']);
            error_log("📦 POST data: " . print_r($_POST, true));
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $produtoId = $_POST['produto_id'] ?? null;
                $variacaoId = $_POST['variacao_id'] ?? null;
                $quantidade = $_POST['quantidade'] ?? 1;
                
                error_log("🔍 Dados recebidos: produto_id=$produtoId, variacao_id=$variacaoId, quantidade=$quantidade");
                
                if (!$produtoId || !$variacaoId) {
                    error_log("❌ Dados inválidos");
                    $this->json(['success' => false, 'message' => 'Dados inválidos']);
                    return;
                }
                
                // Verificar estoque disponível
                $produtoModel = new Produto();
                $estoqueDisponivel = $produtoModel->getEstoque($produtoId, $variacaoId);
                
                error_log("📦 Estoque disponível: $estoqueDisponivel");
                
                if ($estoqueDisponivel < $quantidade) {
                    error_log("❌ Estoque insuficiente");
                    $this->json(['success' => false, 'message' => 'Estoque insuficiente']);
                    return;
                }
                
                $success = Carrinho::adicionar($produtoId, $variacaoId, $quantidade);
                
                error_log("✅ Resultado Carrinho::adicionar: " . ($success ? 'true' : 'false'));
                
                if ($success) {
                    $quantidade_total = Carrinho::getQuantidadeTotal();
                    error_log("📊 Quantidade total no carrinho: $quantidade_total");
                    
                    $this->json([
                        'success' => true, 
                        'message' => 'Produto adicionado ao carrinho',
                        'carrinho_quantidade' => $quantidade_total
                    ]);
                } else {
                    error_log("❌ Falha ao adicionar ao carrinho");
                    $this->json(['success' => false, 'message' => 'Erro ao adicionar produto']);
                }
            } else {
                error_log("❌ Método não é POST");
                $this->json(['success' => false, 'message' => 'Método não permitido']);
            }
        } catch (Exception $e) {
            error_log("💥 Exceção em adicionar(): " . $e->getMessage());
            error_log("📍 Stack trace: " . $e->getTraceAsString());
            $this->json(['success' => false, 'message' => 'Erro interno: ' . $e->getMessage()]);
        }
    }
    
    public function remover() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $produtoId = $_POST['produto_id'] ?? null;
            $variacaoId = $_POST['variacao_id'] ?? null;
            
            if (!$produtoId || !$variacaoId) {
                $this->json(['success' => false, 'message' => 'Dados inválidos']);
                return;
            }
            
            $success = Carrinho::remover($produtoId, $variacaoId);
            
            if ($success) {
                $this->json([
                    'success' => true,
                    'message' => 'Produto removido do carrinho',
                    'carrinho_quantidade' => Carrinho::getQuantidadeTotal(),
                    'subtotal' => Carrinho::getSubtotal()
                ]);
            } else {
                $this->json(['success' => false, 'message' => 'Erro ao remover produto']);
            }
        }
    }
    
    public function atualizar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $produtoId = $_POST['produto_id'] ?? null;
            $variacaoId = $_POST['variacao_id'] ?? null;
            $quantidade = $_POST['quantidade'] ?? 0;
            
            if (!$produtoId || !$variacaoId) {
                $this->json(['success' => false, 'message' => 'Dados inválidos']);
                return;
            }
            
            // Verificar estoque disponível
            $produtoModel = new Produto();
            $estoqueDisponivel = $produtoModel->getEstoque($produtoId, $variacaoId);
            
            if ($quantidade > 0 && $estoqueDisponivel < $quantidade) {
                $this->json(['success' => false, 'message' => 'Estoque insuficiente']);
                return;
            }
            
            $success = Carrinho::atualizar($produtoId, $variacaoId, $quantidade);
            
            if ($success) {
                $pedidoModel = new Pedido();
                $subtotal = Carrinho::getSubtotal();
                $frete = $pedidoModel->calcularFrete($subtotal);
                
                $this->json([
                    'success' => true,
                    'message' => 'Carrinho atualizado',
                    'carrinho_quantidade' => Carrinho::getQuantidadeTotal(),
                    'subtotal' => $subtotal,
                    'frete' => $frete,
                    'total' => $subtotal + $frete
                ]);
            } else {
                $this->json(['success' => false, 'message' => 'Erro ao atualizar carrinho']);
            }
        }
    }
    
    public function limpar() {
        Carrinho::limpar();
        $this->json(['success' => true, 'message' => 'Carrinho limpo']);
    }
}
?> 