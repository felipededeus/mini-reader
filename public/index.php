<?php
// public/index.php

ob_start(); 
session_start();


// Carrega configurações básicas
require_once '../src/Config/config.php';
require_once '../src/Config/Database.php';

// ===============================================
// GUARDA DE ROTA: Redirecionamento Pós-Login
// ===============================================

$is_logged_in = isset($_SESSION['usuario_id']) && isset($_SESSION['usuario_tipo']);
$current_url = isset($_GET['url']) ? $_GET['url'] : 'home'; // A rota atual

// Se o usuário está logado E a URL atual é a home ('/') ou login ('/login')
if ($is_logged_in && ($current_url === 'home' || $current_url === 'login')) {
    $tipo = $_SESSION['usuario_tipo'];
    
    // Redireciona para o dashboard correto
    if ($tipo === 'admin') { // Admin precisa ser o primeiro
        header('Location: ' . BASE_URL . 'admin/dashboard');
        exit;
    } elseif ($tipo === 'professor') {
        header('Location: ' . BASE_URL . 'professor/dashboard');
        exit;
    } elseif ($tipo === 'aluno') {
        header('Location: ' . BASE_URL . 'aluno/dashboard');
        exit;
    }
}

// ===============================================

// Função simples de Autoload
spl_autoload_register(function ($class_name) {
    // Procura em Controllers
    if (file_exists('../src/Controllers/' . $class_name . '.php')) {
        require_once '../src/Controllers/' . $class_name . '.php';
    }
    // Procura em Models
    elseif (file_exists('../src/Models/' . $class_name . '.php')) {
        require_once '../src/Models/' . $class_name . '.php';
    }
});

// Lógica de Roteamento APRIMORADA
$url = isset($_GET['url']) ? $_GET['url'] : 'home';
$url = rtrim($url, '/');
$urlParts = explode('/', $url);

// Define Controller, Método e Parâmetros
$controllerName = ucfirst($urlParts[0]) . 'Controller';
$methodName = isset($urlParts[1]) ? $urlParts[1] : 'index';

// 1. Se o arquivo do Controller não existe, usa HomeController
if (!file_exists('../src/Controllers/' . $controllerName . '.php')) {
    $controllerName = 'HomeController';
    $methodName = 'index';
    $params = [];
} else {
    // 2. Coleta os parâmetros restantes (a partir do índice 2)
    $params = array_slice($urlParts, 2);
}

// Verifica se o arquivo do Controller existe, senão usa o HomeController
// Já verificamos acima, então apenas instanciamos
$controller = new $controllerName();

if (method_exists($controller, $methodName)) {
    
    // AQUI ESTÁ A CHAVE: call_user_func_array é o método correto
    // para chamar o método do Controller, passando o array de parâmetros
    call_user_func_array([$controller, $methodName], $params);
    
} else {
    // Se não encontrou o método, envia 404
    // Para requisições AJAX (que esperam JSON), isso é o que causa o erro de sintaxe!
    header("HTTP/1.0 404 Not Found");
    echo "Erro 404: Página não encontrada (Método inexistente: " . $controllerName . "::" . $methodName . ")";
}

// O buffer de saída será limpo (se for AJAX) ou enviado (se for HTML normal)
ob_end_flush(); 

?>