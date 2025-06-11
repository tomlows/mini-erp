<?php
session_start();

// Configurações básicas
define('BASE_PATH', __DIR__);
define('BASE_URL', 'http://localhost/mini_erp');

// Autoload das classes
spl_autoload_register(function($class) {
    $paths = [
        'controllers/',
        'models/',
        'core/'
    ];
    
    foreach ($paths as $path) {
        $file = BASE_PATH . '/' . $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Roteamento básico
$controller = $_GET['controller'] ?? 'Home';
$action = $_GET['action'] ?? 'index';

$controllerClass = ucfirst($controller) . 'Controller';

if (class_exists($controllerClass)) {
    $controllerInstance = new $controllerClass();
    if (method_exists($controllerInstance, $action)) {
        $controllerInstance->$action();
    } else {
        echo "Ação não encontrada: $action";
    }
} else {
    echo "Controller não encontrado: $controllerClass";
}
?> 