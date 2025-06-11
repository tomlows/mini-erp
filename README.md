# 🏪 Mini ERP - Sistema de Vendas Online

Sistema completo de vendas online com gestão de estoque, pedidos e cupons desenvolvido em PHP puro seguindo padrão MVC.

## 🚀 Características

- **PHP Puro** com arquitetura MVC
- **MySQL** para persistência de dados
- **Bootstrap 5** para interface responsiva
- **AJAX** para interações dinâmicas
- **Integração CEP** via API ViaCEP
- **Sistema de Cupons** com regras de negócio
- **Webhook** para atualizações de status
- **E-mail** de confirmação automático

## 📋 Funcionalidades

### 🛍️ Loja Virtual
- Catálogo de produtos com variações
- Carrinho de compras em sessão
- Cálculo automático de frete
- Aplicação de cupons de desconto
- Finalização de pedidos

### ⚙️ Administração
- **CRUD completo** de produtos e variações
- **Gestão avançada** de variações e estoque individual
- **Deleção segura** com validação de integridade
- **Controle de cupons** promocionais
- **Relatórios** de pedidos

### 🔗 Integrações
- **API ViaCEP**: Busca automática de endereços
- **Webhook**: Recebimento de atualizações de status
- **E-mail**: Confirmação automática de pedidos

## 🛠️ Instalação

### Pré-requisitos
- Apache/Nginx
- PHP 7.4+
- MySQL 5.7+
- Extensões: pdo, pdo_mysql, curl

### Configuração do Banco
```sql
-- Execute o script de criação do banco
mysql -u root -p < database_schema.sql
```

### Configuração do Projeto
1. Clone/baixe o projeto para `/xampp/htdocs/mini_erp/`
2. Configure o banco em `config/database.php`
3. Acesse: `http://localhost/mini_erp/`

## 🎯 Regras de Negócio

### 💸 Cálculo de Frete
- **R$ 52,00 ~ R$ 166,59**: Frete R$ 15,00
- **Acima de R$ 200,00**: Frete GRÁTIS
- **Outros valores**: Frete R$ 20,00

### 🎫 Sistema de Cupons
- Cupons por **porcentagem** ou **valor fixo**
- Validação de **data de expiração**
- **Valor mínimo** de pedido
- **Limite de uso** por cupom

### 📦 Controle de Estoque
- Estoque por **variação** de produto
- Validação na **adição ao carrinho**
- **Redução automática** na finalização
- **Devolução** em cancelamentos

### 🗑️ Sistema de Deleção
- **Produtos**: Deleção completa com validação de integridade
- **Variações**: Remoção individual de variações desnecessárias
- **Proteção**: Impede deleção de itens com pedidos relacionados
- **Transações**: Operações atômicas para garantir consistência

## 🔌 API Endpoints

### Webhook de Status
```
POST /mini_erp/?controller=Webhook&action=pedidoStatus
Content-Type: application/json

{
  "pedido_id": 123,
  "status": "enviado"
}
```

**Status especiais:**
- `cancelado`: Remove pedido e devolve estoque
- Outros: Atualiza status do pedido

### API de CEP
```
GET /mini_erp/?controller=Api&action=buscarCep&cep=01001000
```

## 📊 Estrutura do Banco

```sql
produtos (id, nome, preco, descricao, ativo, created_at)
produto_variacoes (id, produto_id, nome, valor_adicional, ativo)
estoque (id, produto_id, variacao_id, quantidade)
cupons (id, codigo, tipo, valor, valor_minimo, data_expiracao, usado)
pedidos (id, cliente_*, endereco_*, subtotal, desconto, frete, total, status, created_at)
pedido_itens (id, pedido_id, produto_id, variacao_id, quantidade, preco_unitario, preco_total)
```

## 🗂️ Estrutura MVC

```
mini_erp/
├── controllers/          # Controladores da aplicação
│   ├── HomeController.php
│   ├── ProdutoController.php
│   ├── CarrinhoController.php
│   ├── PedidoController.php
│   ├── ApiController.php
│   └── WebhookController.php
├── models/              # Modelos de dados
│   ├── Produto.php
│   ├── Carrinho.php
│   ├── Pedido.php
│   └── Cupom.php
├── views/               # Camada de apresentação
│   ├── layout/          # Templates base
│   ├── home/            # Páginas da loja
│   ├── produtos/        # Gestão de produtos
│   ├── carrinho/        # Carrinho de compras
│   ├── checkout/        # Finalização
│   └── webhook/         # Teste de webhook
├── core/                # Classes fundamentais
│   ├── Database.php     # Conexão com banco
│   └── Controller.php   # Controlador base
├── config/              # Configurações
└── index.php           # Front controller
```

## 🧪 Testes

### Teste do Carrinho
1. Acesse um produto
2. Selecione variação (se houver)
3. Clique em "Adicionar ao Carrinho"
4. Verifique contador no header

### Teste do Checkout
1. Adicione produtos ao carrinho
2. Acesse "Ver Carrinho"
3. Clique em "Finalizar Compra"
4. Preencha dados (CEP será preenchido automaticamente)
5. Aplique cupom (opcional)
6. Confirme pedido

### Teste do Webhook
1. Acesse "Webhook" no menu
2. Digite ID de um pedido existente
3. Selecione um status
4. Clique em "Enviar Webhook"
5. Verifique resposta

## 🎨 Interface

- **Design Responsivo**: Bootstrap 5 com layout mobile-first
- **UX Intuitiva**: Fluxo de compra simplificado
- **Feedback Visual**: Alerts e notificações em tempo real
- **Carregamento Dinâmico**: AJAX para ações do carrinho

## 🔒 Segurança

- **Validação de Entrada**: Sanitização de dados POST/GET
- **SQL Injection**: Prepared statements (PDO)
- **XSS Protection**: htmlspecialchars() nas views
- **CSRF**: Validação de origem nas requisições AJAX

## 📈 Melhorias Futuras

- [ ] Painel administrativo completo
- [ ] Relatórios com gráficos
- [ ] Sistema de usuários e permissões
- [ ] Gateway de pagamento
- [ ] Rastreamento de envios
- [ ] API REST completa

## 🤝 Contribuição

1. Fork o projeto
2. Crie uma branch (`git checkout -b feature/nova-funcionalidade`)
3. Commit suas mudanças (`git commit -am 'Adiciona nova funcionalidade'`)
4. Push para a branch (`git push origin feature/nova-funcionalidade`)
5. Abra um Pull Request

## 📄 Licença

Este projeto é desenvolvido para fins educacionais e está disponível sob licença MIT.

---

**Desenvolvido com ❤️ usando PHP puro + Bootstrap** 