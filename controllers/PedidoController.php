<?php
class PedidoController extends Controller {
    
    public function finalizar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (Carrinho::isEmpty()) {
                $this->json(['success' => false, 'message' => 'Carrinho vazio']);
                return;
            }
            
            $dadosCliente = [
                'nome' => $_POST['nome'],
                'email' => $_POST['email'],
                'telefone' => $_POST['telefone'],
                'cep' => $_POST['cep'],
                'endereco' => $_POST['endereco'],
                'numero' => $_POST['numero'],
                'complemento' => $_POST['complemento'],
                'bairro' => $_POST['bairro'],
                'cidade' => $_POST['cidade'],
                'uf' => $_POST['uf']
            ];
            
            $cupom = null;
            if (!empty($_POST['cupom_codigo'])) {
                $cupomModel = new Cupom();
                $subtotal = Carrinho::getSubtotal();
                $cupom = $cupomModel->validarCupom($_POST['cupom_codigo'], $subtotal);
                
                if (!$cupom) {
                    $this->json(['success' => false, 'message' => 'Cupom inválido ou não aplicável']);
                    return;
                }
            }
            
            try {
                $pedidoModel = new Pedido();
                $carrinho = Carrinho::getItens();
                $pedidoId = $pedidoModel->create($dadosCliente, $carrinho, $cupom);
                
                // Enviar email de confirmação
                $this->enviarEmailConfirmacao($dadosCliente['email'], $pedidoId);
                
                // Limpar carrinho
                Carrinho::limpar();
                
                $this->json([
                    'success' => true, 
                    'message' => 'Pedido finalizado com sucesso!',
                    'pedido_id' => $pedidoId
                ]);
                
            } catch (Exception $e) {
                $this->json(['success' => false, 'message' => 'Erro ao finalizar pedido: ' . $e->getMessage()]);
            }
        }
    }
    
    public function validarCupom() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $codigo = $_POST['codigo'] ?? '';
            $subtotal = Carrinho::getSubtotal();
            
            if (empty($codigo)) {
                $this->json(['success' => false, 'message' => 'Código do cupom é obrigatório']);
                return;
            }
            
            $cupomModel = new Cupom();
            $cupom = $cupomModel->validarCupom($codigo, $subtotal);
            
            if ($cupom) {
                $desconto = $cupomModel->calcularDesconto($cupom, $subtotal);
                $novoSubtotal = $subtotal - $desconto;
                
                $pedidoModel = new Pedido();
                $frete = $pedidoModel->calcularFrete($novoSubtotal);
                
                $this->json([
                    'success' => true,
                    'message' => 'Cupom aplicado com sucesso!',
                    'desconto' => $desconto,
                    'frete' => $frete,
                    'total' => $novoSubtotal + $frete
                ]);
            } else {
                $this->json(['success' => false, 'message' => 'Cupom inválido ou não aplicável']);
            }
        }
    }
    
    public function detalhes() {
        $id = $_GET['id'] ?? null;
        
        if (!$id) {
            $this->redirect('/');
        }
        
        $pedidoModel = new Pedido();
        $pedido = $pedidoModel->getById($id);
        
        if (!$pedido) {
            $this->redirect('/');
        }
        
        $itens = $pedidoModel->getItens($id);
        
        $this->render('pedidos/detalhes', [
            'pedido' => $pedido,
            'itens' => $itens
        ]);
    }
    
    private function enviarEmailConfirmacao($email, $pedidoId) {
        try {
            // Configurações básicas de email
            $to = $email;
            $subject = "Confirmação do Pedido #$pedidoId - Mini ERP";
            
            $message = "
            <html>
            <head>
                <title>Confirmação do Pedido</title>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background: #007bff; color: white; padding: 20px; text-align: center; }
                    .content { padding: 20px; }
                    .footer { background: #f8f9fa; padding: 15px; text-align: center; font-size: 12px; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h2>Mini ERP - Confirmação de Pedido</h2>
                    </div>
                    <div class='content'>
                        <h3>Obrigado pela sua compra!</h3>
                        <p>Seu pedido <strong>#$pedidoId</strong> foi recebido e está sendo processado.</p>
                        <p>Em breve você receberá mais informações sobre o envio.</p>
                        <p>Para acompanhar seu pedido, acesse: <a href='" . BASE_URL . "/?controller=Pedido&action=detalhes&id=$pedidoId'>Detalhes do Pedido</a></p>
                    </div>
                    <div class='footer'>
                        <p>Equipe Mini ERP<br>
                        Este é um e-mail automático, não responda.</p>
                    </div>
                </div>
            </body>
            </html>
            ";
            
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= 'From: Mini ERP <noreply@minierp.com>' . "\r\n";
            $headers .= 'Reply-To: contato@minierp.com' . "\r\n";
            
            // Enviar email (em produção, usar uma biblioteca como PHPMailer)
            $resultado = mail($to, $subject, $message, $headers);
            
            if ($resultado) {
                error_log("✅ Email de confirmação enviado para: $email (Pedido #$pedidoId)");
            } else {
                error_log("❌ Falha ao enviar email para: $email (Pedido #$pedidoId)");
            }
            
            return $resultado;
            
        } catch (Exception $e) {
            error_log("💥 Erro ao enviar email: " . $e->getMessage());
            // Não falhar o pedido por causa do email
            return false;
        }
    }
}
?> 