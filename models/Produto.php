<?php
class Produto {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function getAll() {
        $sql = "SELECT * FROM produtos WHERE ativo = 1 ORDER BY nome";
        return $this->db->query($sql)->fetchAll();
    }
    
    public function getById($id) {
        $sql = "SELECT * FROM produtos WHERE id = ? AND ativo = 1";
        return $this->db->query($sql, [$id])->fetch();
    }
    
    public function getWithVariacoes($id) {
        $produto = $this->getById($id);
        if ($produto) {
            $produto['variacoes'] = $this->getVariacoes($id);
            
            // Se não há variações, criar uma padrão
            if (empty($produto['variacoes'])) {
                $this->criarVariacaoPadrao($id);
                $produto['variacoes'] = $this->getVariacoes($id);
            }
            
            $produto['estoque'] = $this->getEstoque($id);
        }
        return $produto;
    }
    
    public function getVariacoes($produtoId) {
        $sql = "SELECT pv.*, COALESCE(e.quantidade, 0) as estoque 
                FROM produto_variacoes pv 
                LEFT JOIN estoque e ON pv.id = e.variacao_id 
                WHERE pv.produto_id = ? AND pv.ativo = 1";
        return $this->db->query($sql, [$produtoId])->fetchAll();
    }
    
    public function getEstoque($produtoId, $variacaoId = null) {
        if ($variacaoId) {
            $sql = "SELECT quantidade FROM estoque WHERE produto_id = ? AND variacao_id = ?";
            $result = $this->db->query($sql, [$produtoId, $variacaoId])->fetch();
            return $result ? $result['quantidade'] : 0;
        } else {
            $sql = "SELECT SUM(quantidade) as total FROM estoque WHERE produto_id = ?";
            $result = $this->db->query($sql, [$produtoId])->fetch();
            return $result ? $result['total'] : 0;
        }
    }
    
    public function create($data) {
        $sql = "INSERT INTO produtos (nome, preco, descricao) VALUES (?, ?, ?)";
        $this->db->query($sql, [$data['nome'], $data['preco'], $data['descricao']]);
        return $this->db->getConnection()->lastInsertId();
    }
    
    public function update($id, $data) {
        $sql = "UPDATE produtos SET nome = ?, preco = ?, descricao = ? WHERE id = ?";
        return $this->db->query($sql, [$data['nome'], $data['preco'], $data['descricao'], $id]);
    }
    
    public function createVariacao($produtoId, $nome, $valorAdicional = 0) {
        // Verificar se já existe uma variação com esse nome para este produto
        $sql = "SELECT id FROM produto_variacoes WHERE produto_id = ? AND nome = ? AND ativo = 1";
        $variacaoExistente = $this->db->query($sql, [$produtoId, $nome])->fetch();
        
        if ($variacaoExistente) {
            // Se já existe, retornar o ID da variação existente
            return $variacaoExistente['id'];
        }
        
        // Criar nova variação apenas se não existir
        $sql = "INSERT INTO produto_variacoes (produto_id, nome, valor_adicional) VALUES (?, ?, ?)";
        $this->db->query($sql, [$produtoId, $nome, $valorAdicional]);
        return $this->db->getConnection()->lastInsertId();
    }
    
    public function atualizarEstoque($produtoId, $variacaoId, $quantidade) {
        // Verifica se já existe registro de estoque
        $sql = "SELECT id FROM estoque WHERE produto_id = ? AND variacao_id = ?";
        $estoque = $this->db->query($sql, [$produtoId, $variacaoId])->fetch();
        
        if ($estoque) {
            $sql = "UPDATE estoque SET quantidade = ? WHERE produto_id = ? AND variacao_id = ?";
            $this->db->query($sql, [$quantidade, $produtoId, $variacaoId]);
        } else {
            $sql = "INSERT INTO estoque (produto_id, variacao_id, quantidade) VALUES (?, ?, ?)";
            $this->db->query($sql, [$produtoId, $variacaoId, $quantidade]);
        }
    }
    
    public function reduzirEstoque($produtoId, $variacaoId, $quantidade) {
        $sql = "UPDATE estoque SET quantidade = quantidade - ? WHERE produto_id = ? AND variacao_id = ? AND quantidade >= ?";
        $stmt = $this->db->query($sql, [$quantidade, $produtoId, $variacaoId, $quantidade]);
        return $stmt->rowCount() > 0;
    }
    
    private function criarVariacaoPadrao($produtoId) {
        // Verificar se já existe uma variação "Padrão" para evitar duplicações
        $sql = "SELECT id FROM produto_variacoes WHERE produto_id = ? AND nome = 'Padrão' AND ativo = 1";
        $variacaoExistente = $this->db->query($sql, [$produtoId])->fetch();
        
        if ($variacaoExistente) {
            return $variacaoExistente['id'];
        }
        
        // Criar variação padrão apenas se não existir
        $variacaoId = $this->createVariacao($produtoId, 'Padrão', 0);
        
        // Criar estoque inicial de 10 unidades
        $this->atualizarEstoque($produtoId, $variacaoId, 10);
        
        return $variacaoId;
    }
    
    public function hasPedidosRelacionados($produtoId) {
        $sql = "SELECT COUNT(*) as total FROM pedido_itens WHERE produto_id = ?";
        $result = $this->db->query($sql, [$produtoId])->fetch();
        return $result['total'] > 0;
    }
    
    public function delete($id) {
        try {
            // Verificar se há pedidos relacionados
            if ($this->hasPedidosRelacionados($id)) {
                return false;
            }
            
            $this->db->getConnection()->beginTransaction();
            
            // Deletar estoque
            $sql = "DELETE FROM estoque WHERE produto_id = ?";
            $this->db->query($sql, [$id]);
            
            // Deletar variações
            $sql = "DELETE FROM produto_variacoes WHERE produto_id = ?";
            $this->db->query($sql, [$id]);
            
            // Deletar produto
            $sql = "DELETE FROM produtos WHERE id = ?";
            $this->db->query($sql, [$id]);
            
            $this->db->getConnection()->commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->getConnection()->rollback();
            throw $e;
        }
    }
    
    public function hasVariacaoInPedidos($variacaoId) {
        $sql = "SELECT COUNT(*) as total FROM pedido_itens WHERE variacao_id = ?";
        $result = $this->db->query($sql, [$variacaoId])->fetch();
        return $result['total'] > 0;
    }
    
    public function deleteVariacao($variacaoId) {
        try {
            // Verificar se há pedidos relacionados
            if ($this->hasVariacaoInPedidos($variacaoId)) {
                return false;
            }
            
            $this->db->getConnection()->beginTransaction();
            
            // Deletar estoque da variação
            $sql = "DELETE FROM estoque WHERE variacao_id = ?";
            $this->db->query($sql, [$variacaoId]);
            
            // Deletar variação
            $sql = "DELETE FROM produto_variacoes WHERE id = ?";
            $this->db->query($sql, [$variacaoId]);
            
            $this->db->getConnection()->commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->getConnection()->rollback();
            throw $e;
        }
    }
    
    public function countVariacoes($produtoId) {
        $sql = "SELECT COUNT(*) as total FROM produto_variacoes WHERE produto_id = ? AND ativo = 1";
        $result = $this->db->query($sql, [$produtoId])->fetch();
        return $result['total'];
    }
}
?> 