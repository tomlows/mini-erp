<?php
class Pedido {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function calcularFrete($subtotal) {
        if ($subtotal >= 200) {
            return 0; // Frete grátis
        } elseif ($subtotal >= 52 && $subtotal <= 166.59) {
            return 15.00;
        } else {
            return 20.00;
        }
    }
    
    public function create($dadosCliente, $carrinho, $cupom = null) {
        try {
            $this->db->getConnection()->beginTransaction();
            
            $subtotal = 0;
            foreach ($carrinho as $item) {
                $subtotal += $item['preco_total'];
            }
            
            $desconto = 0;
            $cupomId = null;
            
            if ($cupom) {
                $cupomModel = new Cupom();
                $desconto = $cupomModel->calcularDesconto($cupom, $subtotal);
                $cupomId = $cupom['id'];
            }
            
            $frete = $this->calcularFrete($subtotal - $desconto);
            $total = $subtotal - $desconto + $frete;
            
            // Criar pedido
            $sql = "INSERT INTO pedidos (cliente_nome, cliente_email, cliente_telefone, cep, endereco, numero, complemento, bairro, cidade, uf, subtotal, desconto, frete, total, cupom_id) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $this->db->query($sql, [
                $dadosCliente['nome'],
                $dadosCliente['email'],
                $dadosCliente['telefone'],
                $dadosCliente['cep'],
                $dadosCliente['endereco'],
                $dadosCliente['numero'],
                $dadosCliente['complemento'],
                $dadosCliente['bairro'],
                $dadosCliente['cidade'],
                $dadosCliente['uf'],
                $subtotal,
                $desconto,
                $frete,
                $total,
                $cupomId
            ]);
            
            $pedidoId = $this->db->getConnection()->lastInsertId();
            
            // Criar itens do pedido e reduzir estoque
            $produtoModel = new Produto();
            foreach ($carrinho as $item) {
                $sql = "INSERT INTO pedido_itens (pedido_id, produto_id, variacao_id, quantidade, preco_unitario, preco_total) 
                        VALUES (?, ?, ?, ?, ?, ?)";
                
                $this->db->query($sql, [
                    $pedidoId,
                    $item['produto_id'],
                    $item['variacao_id'],
                    $item['quantidade'],
                    $item['preco_unitario'],
                    $item['preco_total']
                ]);
                
                // Reduzir estoque
                $produtoModel->reduzirEstoque($item['produto_id'], $item['variacao_id'], $item['quantidade']);
            }
            
            // Usar cupom se aplicado
            if ($cupom) {
                $cupomModel = new Cupom();
                $cupomModel->usarCupom($cupom['id']);
            }
            
            $this->db->getConnection()->commit();
            return $pedidoId;
            
        } catch (Exception $e) {
            $this->db->getConnection()->rollback();
            throw $e;
        }
    }
    
    public function getById($id) {
        $sql = "SELECT p.*, c.codigo as cupom_codigo FROM pedidos p 
                LEFT JOIN cupons c ON p.cupom_id = c.id 
                WHERE p.id = ?";
        return $this->db->query($sql, [$id])->fetch();
    }
    
    public function getItens($pedidoId) {
        $sql = "SELECT pi.*, pr.nome as produto_nome, pv.nome as variacao_nome 
                FROM pedido_itens pi
                JOIN produtos pr ON pi.produto_id = pr.id
                LEFT JOIN produto_variacoes pv ON pi.variacao_id = pv.id
                WHERE pi.pedido_id = ?";
        return $this->db->query($sql, [$pedidoId])->fetchAll();
    }
    
    public function updateStatus($id, $status) {
        try {
            // Validar status
            $validStatuses = ['pendente', 'confirmado', 'processando', 'enviado', 'entregue', 'cancelado'];
            if (!in_array($status, $validStatuses)) {
                throw new Exception("Status inválido: $status");
            }
            
            $sql = "UPDATE pedidos SET status = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
            $stmt = $this->db->query($sql, [$status, $id]);
            
            // Log da mudança
            error_log("📝 Status do pedido $id atualizado para: $status");
            
            return $stmt->rowCount() > 0;
            
        } catch (Exception $e) {
            error_log("❌ Erro ao atualizar status do pedido $id: " . $e->getMessage());
            throw $e;
        }
    }
    
    public function cancelar($id) {
        try {
            $this->db->getConnection()->beginTransaction();
            
            // Verificar se o pedido existe
            $pedido = $this->getById($id);
            if (!$pedido) {
                throw new Exception("Pedido $id não encontrado");
            }
            
            error_log("🗑️ Cancelando pedido $id - Cliente: {$pedido['cliente_nome']}");
            
            // Obter itens do pedido para devolver estoque
            $itens = $this->getItens($id);
            
            if (!empty($itens)) {
                foreach ($itens as $item) {
                    // Devolver estoque
                    $sql = "UPDATE estoque SET quantidade = quantidade + ? WHERE produto_id = ? AND variacao_id = ?";
                    $this->db->query($sql, [$item['quantidade'], $item['produto_id'], $item['variacao_id']]);
                    
                    error_log("📦 Estoque devolvido - Produto: {$item['produto_id']}, Variação: {$item['variacao_id']}, Quantidade: {$item['quantidade']}");
                }
            }
            
            // Incrementar usos do cupom se foi usado (devolver o uso)
            if ($pedido['cupom_id']) {
                $sql = "UPDATE cupons SET usos_atual = GREATEST(0, usos_atual - 1) WHERE id = ?";
                $this->db->query($sql, [$pedido['cupom_id']]);
                error_log("🎫 Uso do cupom {$pedido['cupom_id']} devolvido");
            }
            
            // Deletar itens do pedido primeiro (foreign key)
            $sql = "DELETE FROM pedido_itens WHERE pedido_id = ?";
            $this->db->query($sql, [$id]);
            
            // Deletar pedido
            $sql = "DELETE FROM pedidos WHERE id = ?";
            $this->db->query($sql, [$id]);
            
            $this->db->getConnection()->commit();
            
            error_log("✅ Pedido $id cancelado e removido com sucesso");
            return true;
            
        } catch (Exception $e) {
            $this->db->getConnection()->rollback();
            error_log("❌ Erro ao cancelar pedido $id: " . $e->getMessage());
            return false;
        }
    }
    
    public function getAll() {
        $sql = "SELECT * FROM pedidos ORDER BY created_at DESC";
        return $this->db->query($sql)->fetchAll();
    }
    
    public function getRecentes($limite = 10) {
        // Validar limite para evitar SQL injection
        $limite = (int) $limite;
        if ($limite <= 0) $limite = 10;
        if ($limite > 100) $limite = 100; // Máximo de 100 registros
        
        $sql = "SELECT * FROM pedidos ORDER BY created_at DESC LIMIT " . $limite;
        return $this->db->query($sql)->fetchAll();
    }
}
?> 