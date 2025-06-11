<?php
class HomeController extends Controller {
    
    public function index() {
        $produtoModel = new Produto();
        $produtos = $produtoModel->getAll();
        
        $this->render('home/index', [
            'produtos' => $produtos,
            'carrinho_quantidade' => Carrinho::getQuantidadeTotal()
        ]);
    }
    
    public function produto() {
        $id = $_GET['id'] ?? null;
        
        if (!$id) {
            $this->redirect('/');
        }
        
        $produtoModel = new Produto();
        $produto = $produtoModel->getWithVariacoes($id);
        
        if (!$produto) {
            $this->redirect('/');
        }
        
        $this->render('produtos/detalhes', [
            'produto' => $produto,
            'carrinho_quantidade' => Carrinho::getQuantidadeTotal()
        ]);
    }
    
    public function carrinho() {
        $itens = Carrinho::getItens();
        $subtotal = Carrinho::getSubtotal();
        
        $pedidoModel = new Pedido();
        $frete = $pedidoModel->calcularFrete($subtotal);
        
        $this->render('carrinho/index', [
            'itens' => $itens,
            'subtotal' => $subtotal,
            'frete' => $frete,
            'total' => $subtotal + $frete,
            'carrinho_quantidade' => Carrinho::getQuantidadeTotal()
        ]);
    }
    
    public function checkout() {
        if (Carrinho::isEmpty()) {
            $this->redirect('/');
        }
        
        $itens = Carrinho::getItens();
        $subtotal = Carrinho::getSubtotal();
        
        $pedidoModel = new Pedido();
        $frete = $pedidoModel->calcularFrete($subtotal);
        
        $this->render('checkout/index', [
            'itens' => $itens,
            'subtotal' => $subtotal,
            'frete' => $frete,
            'total' => $subtotal + $frete,
            'carrinho_quantidade' => Carrinho::getQuantidadeTotal()
        ]);
    }
}
?> 