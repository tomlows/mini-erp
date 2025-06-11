<?php
class Controller {
    protected function render($view, $data = []) {
        extract($data);
        $viewFile = BASE_PATH . '/views/' . $view . '.php';
        
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            echo "View não encontrada: $view";
        }
    }
    
    protected function redirect($url) {
        header('Location: ' . BASE_URL . $url);
        exit;
    }
    
    protected function json($data) {
        // Limpar qualquer output anterior
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        // Iniciar novo buffer
        ob_start();
        
        // Tentar definir header se ainda possível
        if (!headers_sent()) {
            header('Content-Type: application/json');
        }
        
        echo json_encode($data);
        ob_end_flush();
        exit;
    }
}
?> 