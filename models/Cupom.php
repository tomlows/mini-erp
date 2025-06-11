<?php
class Cupom {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function validarCupom($codigo, $subtotal) {
        $sql = "SELECT * FROM cupons WHERE codigo = ? AND ativo = 1 
                AND data_inicio <= CURDATE() AND data_fim >= CURDATE()
                AND valor_minimo_pedido <= ?
                AND (limite_uso IS NULL OR usos_atual < limite_uso)";
        
        $cupom = $this->db->query($sql, [$codigo, $subtotal])->fetch();
        
        if (!$cupom) {
            return false;
        }
        
        return $cupom;
    }
    
    public function calcularDesconto($cupom, $subtotal) {
        if ($cupom['tipo'] === 'percentual') {
            return ($subtotal * $cupom['valor']) / 100;
        } else {
            return $cupom['valor'];
        }
    }
    
    public function usarCupom($cupomId) {
        $sql = "UPDATE cupons SET usos_atual = usos_atual + 1 WHERE id = ?";
        $this->db->query($sql, [$cupomId]);
    }
    
    public function getAll() {
        $sql = "SELECT * FROM cupons ORDER BY created_at DESC";
        return $this->db->query($sql)->fetchAll();
    }
    
    public function create($data) {
        $sql = "INSERT INTO cupons (codigo, tipo, valor, valor_minimo_pedido, data_inicio, data_fim, limite_uso) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $this->db->query($sql, [
            $data['codigo'],
            $data['tipo'], 
            $data['valor'],
            $data['valor_minimo_pedido'],
            $data['data_inicio'],
            $data['data_fim'],
            $data['limite_uso']
        ]);
        
        return $this->db->getConnection()->lastInsertId();
    }
}
?> 