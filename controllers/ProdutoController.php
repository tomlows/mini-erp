<?php
class ProdutoController extends Controller {
    
    public function index() {
        $produtoModel = new Produto();
        $produtos = $produtoModel->getAll();
        
        $this->render('admin/produtos/index', [
            'produtos' => $produtos
        ]);
    }
    
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $produtoModel = new Produto();
            
            $data = [
                'nome' => $_POST['nome'],
                'preco' => $_POST['preco'],
                'descricao' => $_POST['descricao']
            ];
            
            $produtoId = $produtoModel->create($data);
            
            // Criar variações se fornecidas
            if (!empty($_POST['variacoes'])) {
                foreach ($_POST['variacoes'] as $variacao) {
                    if (!empty($variacao['nome'])) {
                        $variacaoId = $produtoModel->createVariacao(
                            $produtoId, 
                            $variacao['nome'], 
                            $variacao['valor_adicional'] ?? 0
                        );
                        
                        // Criar estoque para a variação
                        if (isset($variacao['estoque'])) {
                            $produtoModel->atualizarEstoque($produtoId, $variacaoId, $variacao['estoque']);
                        }
                    }
                }
            }
            
            $this->redirect('/?controller=Produto&action=index');
        }
        
        $this->render('admin/produtos/create');
    }
    
    public function edit() {
        $id = $_GET['id'] ?? null;
        
        if (!$id) {
            $this->redirect('/?controller=Produto&action=index');
        }
        
        $produtoModel = new Produto();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nome' => $_POST['nome'],
                'preco' => $_POST['preco'],
                'descricao' => $_POST['descricao']
            ];
            
            $produtoModel->update($id, $data);
            
            // Atualizar estoque das variações
            if (!empty($_POST['estoque'])) {
                foreach ($_POST['estoque'] as $variacaoId => $quantidade) {
                    $produtoModel->atualizarEstoque($id, $variacaoId, $quantidade);
                }
            }
            
            $this->redirect('/?controller=Produto&action=index');
        }
        
        $produto = $produtoModel->getWithVariacoes($id);
        
        if (!$produto) {
            $this->redirect('/?controller=Produto&action=index');
        }
        
        $this->render('admin/produtos/edit', [
            'produto' => $produto
        ]);
    }
    
    public function adicionarVariacao() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $produtoModel = new Produto();
                
                $produtoId = $_POST['produto_id'] ?? null;
                $nome = trim($_POST['nome'] ?? '');
                $valorAdicional = $_POST['valor_adicional'] ?? 0;
                $estoque = $_POST['estoque'] ?? 0;
                
                // Validações
                if (!$produtoId || !$nome) {
                    $this->json(['success' => false, 'message' => 'Produto ID e nome da variação são obrigatórios']);
                    return;
                }
                
                // Verificar se já existe variação com esse nome para este produto
                $db = Database::getInstance();
                $variacaoExistente = $db->query(
                    "SELECT id FROM produto_variacoes WHERE produto_id = ? AND nome = ? AND ativo = 1", 
                    [$produtoId, $nome]
                )->fetch();
                
                if ($variacaoExistente) {
                    $this->json(['success' => false, 'message' => 'Já existe uma variação com este nome para este produto']);
                    return;
                }
                
                $variacaoId = $produtoModel->createVariacao($produtoId, $nome, $valorAdicional);
                
                if (!empty($estoque)) {
                    $produtoModel->atualizarEstoque($produtoId, $variacaoId, $estoque);
                }
                
                $this->json([
                    'success' => true, 
                    'variacao_id' => $variacaoId,
                    'message' => 'Variação adicionada com sucesso!'
                ]);
                
            } catch (Exception $e) {
                error_log("❌ Erro ao adicionar variação: " . $e->getMessage());
                $this->json(['success' => false, 'message' => 'Erro interno: ' . $e->getMessage()]);
            }
        }
    }
    
    public function delete() {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->json(['success' => false, 'message' => 'Método não permitido']);
                return;
            }
            
            $id = $_POST['id'] ?? null;
            
            if (!$id) {
                $this->json(['success' => false, 'message' => 'ID do produto é obrigatório']);
                return;
            }
            
            $produtoModel = new Produto();
            
            // Verificar se o produto existe
            $produto = $produtoModel->getById($id);
            if (!$produto) {
                $this->json(['success' => false, 'message' => 'Produto não encontrado']);
                return;
            }
            
            // Verificar se há pedidos relacionados
            if ($produtoModel->hasPedidosRelacionados($id)) {
                $this->json([
                    'success' => false, 
                    'message' => 'Não é possível deletar este produto pois ele possui pedidos relacionados. Para manter a integridade dos dados, produtos com pedidos não podem ser removidos.'
                ]);
                return;
            }
            
            // Deletar produto usando o modelo
            $resultado = $produtoModel->delete($id);
            
            if ($resultado) {
                error_log("✅ Produto deletado com sucesso: ID $id - {$produto['nome']}");
                
                $this->json([
                    'success' => true, 
                    'message' => 'Produto deletado com sucesso!'
                ]);
            } else {
                $this->json([
                    'success' => false, 
                    'message' => 'Não foi possível deletar o produto'
                ]);
            }
            
        } catch (Exception $e) {
            error_log("❌ Erro ao deletar produto: " . $e->getMessage());
            error_log("📍 Stack trace: " . $e->getTraceAsString());
            
            $this->json([
                'success' => false, 
                'message' => 'Erro interno ao deletar produto: ' . $e->getMessage()
            ]);
        }
    }
    
    public function deletarVariacao() {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->json(['success' => false, 'message' => 'Método não permitido']);
                return;
            }
            
            $variacaoId = $_POST['variacao_id'] ?? null;
            $produtoId = $_POST['produto_id'] ?? null;
            
            if (!$variacaoId || !$produtoId) {
                $this->json(['success' => false, 'message' => 'ID da variação e produto são obrigatórios']);
                return;
            }
            
            $produtoModel = new Produto();
            $db = Database::getInstance();
            
            // Verificar se a variação existe
            $variacao = $db->query(
                "SELECT * FROM produto_variacoes WHERE id = ? AND produto_id = ?", 
                [$variacaoId, $produtoId]
            )->fetch();
            
            if (!$variacao) {
                $this->json(['success' => false, 'message' => 'Variação não encontrada']);
                return;
            }
            
            // Verificar se há pedidos com esta variação
            $pedidosRelacionados = $db->query(
                "SELECT COUNT(*) as total FROM pedido_itens WHERE produto_id = ? AND variacao_id = ?", 
                [$produtoId, $variacaoId]
            )->fetch();
            
            if ($pedidosRelacionados['total'] > 0) {
                $this->json([
                    'success' => false, 
                    'message' => 'Não é possível deletar esta variação pois ela possui pedidos relacionados. Total de pedidos: ' . $pedidosRelacionados['total']
                ]);
                return;
            }
            
            // Verificar quantas variações o produto tem
            $totalVariacoes = $db->query(
                "SELECT COUNT(*) as total FROM produto_variacoes WHERE produto_id = ? AND ativo = 1", 
                [$produtoId]
            )->fetch();
            
            if ($totalVariacoes['total'] <= 1) {
                $this->json([
                    'success' => false, 
                    'message' => 'Não é possível deletar a única variação do produto. Um produto deve ter pelo menos uma variação.'
                ]);
                return;
            }
            
            // Deletar variação usando o modelo
            $resultado = $produtoModel->deleteVariacao($variacaoId);
            
            if ($resultado) {
                error_log("✅ Variação deletada com sucesso: ID $variacaoId - {$variacao['nome']} (Produto: $produtoId)");
                
                $this->json([
                    'success' => true, 
                    'message' => 'Variação deletada com sucesso!'
                ]);
            } else {
                $this->json([
                    'success' => false, 
                    'message' => 'Não foi possível deletar a variação'
                ]);
            }
            
        } catch (Exception $e) {
            error_log("❌ Erro ao deletar variação: " . $e->getMessage());
            error_log("📍 Stack trace: " . $e->getTraceAsString());
            
            $this->json([
                'success' => false, 
                'message' => 'Erro interno ao deletar variação: ' . $e->getMessage()
            ]);
        }
    }
}
?> 