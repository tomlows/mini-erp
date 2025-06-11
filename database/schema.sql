-- ===================================
-- MINI ERP - SISTEMA DE VENDAS ONLINE
-- ===================================
-- Criação do banco de dados
CREATE DATABASE IF NOT EXISTS mini_erp DEFAULT CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE mini_erp;

-- ===================================
-- TABELA DE PRODUTOS (Base)
-- ===================================
-- Armazena informações básicas dos produtos
CREATE TABLE produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL COMMENT 'Nome do produto (ex: Camiseta, Calça)',
    preco DECIMAL(10,2) NOT NULL COMMENT 'Preço base do produto',
    descricao TEXT COMMENT 'Descrição detalhada do produto',
    ativo BOOLEAN DEFAULT TRUE COMMENT 'Se o produto está ativo para venda',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ===================================
-- TABELA DE VARIAÇÕES DOS PRODUTOS
-- ===================================
-- Cada produto pode ter diferentes variações (tamanhos, cores, etc.)
-- Exemplo: Camiseta pode ter P, M, G, GG
CREATE TABLE produto_variacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    produto_id INT NOT NULL COMMENT 'ID do produto pai',
    nome VARCHAR(255) NOT NULL COMMENT 'Nome da variação (P, M, G, Azul, etc.)',
    valor_adicional DECIMAL(10,2) DEFAULT 0 COMMENT 'Valor adicional ao preço base (pode ser 0)',
    ativo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (produto_id) REFERENCES produtos(id) ON DELETE CASCADE,
    
    -- Evitar variações duplicadas para o mesmo produto
    UNIQUE KEY uk_produto_variacao (produto_id, nome)
);

-- ===================================
-- TABELA DE ESTOQUE
-- ===================================
-- Controla a quantidade disponível de cada variação
-- Cada variação tem seu próprio estoque independente
CREATE TABLE estoque (
    id INT AUTO_INCREMENT PRIMARY KEY,
    produto_id INT NOT NULL COMMENT 'ID do produto',
    variacao_id INT NOT NULL COMMENT 'ID da variação específica',
    quantidade INT NOT NULL DEFAULT 0 COMMENT 'Quantidade em estoque desta variação',
    quantidade_minima INT DEFAULT 5 COMMENT 'Estoque mínimo para alerta',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (produto_id) REFERENCES produtos(id) ON DELETE CASCADE,
    FOREIGN KEY (variacao_id) REFERENCES produto_variacoes(id) ON DELETE CASCADE,
    
    -- Cada variação tem apenas um registro de estoque
    UNIQUE KEY uk_estoque_variacao (produto_id, variacao_id)
);

-- ===================================
-- TABELA DE CUPONS DE DESCONTO
-- ===================================
CREATE TABLE cupons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(50) UNIQUE NOT NULL COMMENT 'Código do cupom (ex: DESC10)',
    tipo ENUM('percentual', 'valor_fixo') NOT NULL COMMENT 'Tipo de desconto',
    valor DECIMAL(10,2) NOT NULL COMMENT 'Valor do desconto (% ou R$)',
    valor_minimo_pedido DECIMAL(10,2) DEFAULT 0 COMMENT 'Valor mínimo para usar o cupom',
    data_inicio DATE NOT NULL,
    data_fim DATE NOT NULL,
    limite_uso INT DEFAULT NULL COMMENT 'Limite de usos (NULL = ilimitado)',
    usos_atual INT DEFAULT 0 COMMENT 'Quantas vezes já foi usado',
    ativo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ===================================
-- TABELA DE PEDIDOS
-- ===================================
CREATE TABLE pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_nome VARCHAR(255) NOT NULL,
    cliente_email VARCHAR(255) NOT NULL,
    cliente_telefone VARCHAR(20),
    cep VARCHAR(10) NOT NULL,
    endereco VARCHAR(255) NOT NULL,
    numero VARCHAR(10) NOT NULL,
    complemento VARCHAR(100),
    bairro VARCHAR(100) NOT NULL,
    cidade VARCHAR(100) NOT NULL,
    uf VARCHAR(2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL COMMENT 'Total dos produtos',
    desconto DECIMAL(10,2) DEFAULT 0 COMMENT 'Desconto aplicado',
    frete DECIMAL(10,2) NOT NULL COMMENT 'Valor do frete',
    total DECIMAL(10,2) NOT NULL COMMENT 'Valor final do pedido',
    cupom_id INT NULL COMMENT 'Cupom utilizado (se houver)',
    status ENUM('pendente', 'confirmado', 'enviado', 'entregue', 'cancelado') DEFAULT 'pendente',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (cupom_id) REFERENCES cupons(id)
);

-- ===================================
-- TABELA DE ITENS DO PEDIDO
-- ===================================
-- Cada linha representa um item específico no pedido
CREATE TABLE pedido_itens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT NOT NULL,
    produto_id INT NOT NULL COMMENT 'Produto comprado',
    variacao_id INT NOT NULL COMMENT 'Variação específica comprada',
    quantidade INT NOT NULL COMMENT 'Quantidade comprada desta variação',
    preco_unitario DECIMAL(10,2) NOT NULL COMMENT 'Preço por unidade na data da compra',
    preco_total DECIMAL(10,2) NOT NULL COMMENT 'Preço total deste item (unitário x quantidade)',
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
    FOREIGN KEY (produto_id) REFERENCES produtos(id),
    FOREIGN KEY (variacao_id) REFERENCES produto_variacoes(id)
);

-- ===================================
-- DADOS DE EXEMPLO MAIS DIDÁTICOS
-- ===================================

-- 1. PRODUTOS BASE
INSERT INTO produtos (nome, preco, descricao) VALUES
('Camiseta Básica', 29.90, 'Camiseta 100% algodão, confortável e versátil'),
('Calça Jeans Feminina', 89.90, 'Calça jeans skinny, modelagem moderna'),
('Tênis Esportivo', 149.90, 'Tênis para corrida e caminhada, muito confortável');

-- 2. VARIAÇÕES DOS PRODUTOS
-- Camiseta Básica - Tamanhos diferentes com preços graduais
INSERT INTO produto_variacoes (produto_id, nome, valor_adicional) VALUES
-- Camiseta (ID 1) - Tamanhos P, M, G, GG
(1, 'Tamanho P', 0.00),      -- Preço base: R$ 29,90
(1, 'Tamanho M', 0.00),      -- Preço base: R$ 29,90  
(1, 'Tamanho G', 5.00),      -- Preço final: R$ 34,90
(1, 'Tamanho GG', 10.00),    -- Preço final: R$ 39,90

-- Calça Jeans (ID 2) - Numerações femininas
(2, 'Numeração 36', 0.00),   -- Preço base: R$ 89,90
(2, 'Numeração 38', 0.00),   -- Preço base: R$ 89,90
(2, 'Numeração 40', 0.00),   -- Preço base: R$ 89,90
(2, 'Numeração 42', 5.00),   -- Preço final: R$ 94,90

-- Tênis Esportivo (ID 3) - Numerações diversas
(3, 'Número 37', 0.00),      -- Preço base: R$ 149,90
(3, 'Número 38', 0.00),      -- Preço base: R$ 149,90
(3, 'Número 39', 0.00),      -- Preço base: R$ 149,90
(3, 'Número 40', 0.00),      -- Preço base: R$ 149,90
(3, 'Número 41', 0.00),      -- Preço base: R$ 149,90
(3, 'Número 42', 0.00);      -- Preço base: R$ 149,90

-- 3. ESTOQUE INICIAL PARA CADA VARIAÇÃO
INSERT INTO estoque (produto_id, variacao_id, quantidade, quantidade_minima) VALUES
-- Camiseta Básica - Estoque por tamanho
(1, 1, 50, 10),  -- P: 50 unidades (estoque alto)
(1, 2, 30, 10),  -- M: 30 unidades (estoque médio)
(1, 3, 25, 5),   -- G: 25 unidades (estoque baixo)
(1, 4, 15, 5),   -- GG: 15 unidades (estoque crítico)

-- Calça Jeans - Estoque por numeração
(2, 5, 20, 5),   -- 36: 20 unidades
(2, 6, 25, 5),   -- 38: 25 unidades (tamanho mais vendido)
(2, 7, 30, 5),   -- 40: 30 unidades (tamanho mais vendido)
(2, 8, 15, 5),   -- 42: 15 unidades

-- Tênis Esportivo - Estoque por número
(3, 9, 10, 3),   -- 37: 10 unidades
(3, 10, 15, 3),  -- 38: 15 unidades
(3, 11, 20, 3),  -- 39: 20 unidades (número mais comum)
(3, 12, 18, 3),  -- 40: 18 unidades (número mais comum)
(3, 13, 12, 3),  -- 41: 12 unidades
(3, 14, 8, 3);   -- 42: 8 unidades

-- 4. CUPONS DE DESCONTO PARA TESTES
INSERT INTO cupons (codigo, tipo, valor, valor_minimo_pedido, data_inicio, data_fim, limite_uso) VALUES
('PRIMEIRA10', 'percentual', 10.00, 50.00, '2024-01-01', '2024-12-31', 100),
('FRETE15', 'valor_fixo', 15.00, 100.00, '2024-01-01', '2024-12-31', 50),
('VIP20', 'percentual', 20.00, 200.00, '2024-01-01', '2024-12-31', 25);

-- ===================================
-- EXEMPLO DE COMO FUNCIONA O SISTEMA:
-- ===================================
/*
PRODUTO: Camiseta Básica (R$ 29,90)
├── Variação P (R$ 29,90 + R$ 0,00 = R$ 29,90) - Estoque: 50
├── Variação M (R$ 29,90 + R$ 0,00 = R$ 29,90) - Estoque: 30  
├── Variação G (R$ 29,90 + R$ 5,00 = R$ 34,90) - Estoque: 25
└── Variação GG (R$ 29,90 + R$ 10,00 = R$ 39,90) - Estoque: 15

PRODUTO: Calça Jeans (R$ 89,90)
├── Numeração 36 (R$ 89,90 + R$ 0,00 = R$ 89,90) - Estoque: 20
├── Numeração 38 (R$ 89,90 + R$ 0,00 = R$ 89,90) - Estoque: 25
├── Numeração 40 (R$ 89,90 + R$ 0,00 = R$ 89,90) - Estoque: 30
└── Numeração 42 (R$ 89,90 + R$ 5,00 = R$ 94,90) - Estoque: 15

COMO COMPRAR:
1. Cliente escolhe "Camiseta Básica"
2. Seleciona variação "Tamanho G" 
3. Preço final: R$ 34,90 (base + adicional)
4. Estoque reduzido de 25 para 24 unidades
*/ 