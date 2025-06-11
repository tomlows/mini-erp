<?php
class Carrinho {
    
    public static function adicionar($produtoId, $variacaoId, $quantidade = 1) {
        if (!isset($_SESSION['carrinho'])) {
            $_SESSION['carrinho'] = [];
        }
        
        $key = $produtoId . '_' . $variacaoId;
        
        if (isset($_SESSION['carrinho'][$key])) {
            $_SESSION['carrinho'][$key]['quantidade'] += $quantidade;
        } else {
            $produtoModel = new Produto();
            $produto = $produtoModel->getById($produtoId);
            
            if (!$produto) {
                return false;
            }
            
            // Buscar variação
            $variacao = null;
            if ($variacaoId) {
                $sql = "SELECT * FROM produto_variacoes WHERE id = ? AND produto_id = ?";
                $db = Database::getInstance();
                $variacao = $db->query($sql, [$variacaoId, $produtoId])->fetch();
            }
            
            $precoUnitario = $produto['preco'] + ($variacao ? $variacao['valor_adicional'] : 0);
            
            $_SESSION['carrinho'][$key] = [
                'produto_id' => $produtoId,
                'variacao_id' => $variacaoId,
                'produto_nome' => $produto['nome'],
                'variacao_nome' => $variacao ? $variacao['nome'] : null,
                'quantidade' => $quantidade,
                'preco_unitario' => $precoUnitario,
                'preco_total' => $precoUnitario * $quantidade
            ];
        }
        
        // Atualizar preço total
        $_SESSION['carrinho'][$key]['preco_total'] = 
            $_SESSION['carrinho'][$key]['preco_unitario'] * $_SESSION['carrinho'][$key]['quantidade'];
        
        return true;
    }
    
    public static function remover($produtoId, $variacaoId) {
        $key = $produtoId . '_' . $variacaoId;
        if (isset($_SESSION['carrinho'][$key])) {
            unset($_SESSION['carrinho'][$key]);
            return true;
        }
        return false;
    }
    
    public static function atualizar($produtoId, $variacaoId, $quantidade) {
        $key = $produtoId . '_' . $variacaoId;
        if (isset($_SESSION['carrinho'][$key])) {
            if ($quantidade <= 0) {
                return self::remover($produtoId, $variacaoId);
            }
            
            $_SESSION['carrinho'][$key]['quantidade'] = $quantidade;
            $_SESSION['carrinho'][$key]['preco_total'] = 
                $_SESSION['carrinho'][$key]['preco_unitario'] * $quantidade;
            return true;
        }
        return false;
    }
    
    public static function getItens() {
        return $_SESSION['carrinho'] ?? [];
    }
    
    public static function getSubtotal() {
        $subtotal = 0;
        foreach (self::getItens() as $item) {
            $subtotal += $item['preco_total'];
        }
        return $subtotal;
    }
    
    public static function getQuantidadeTotal() {
        $total = 0;
        foreach (self::getItens() as $item) {
            $total += $item['quantidade'];
        }
        return $total;
    }
    
    public static function limpar() {
        unset($_SESSION['carrinho']);
    }
    
    public static function isEmpty() {
        return empty($_SESSION['carrinho']);
    }
}
?> 