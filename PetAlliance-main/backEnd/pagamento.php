<?php
    require_once __DIR__ . "/config/config.php";
    require_once __DIR__ . "/controllers/api/PagamentoController.php";
    
    session_start();

    if (isset($_GET['route']) && $_GET['route'] === 'webhook') {
        $controller = new PagamentoController();
        header('Content-Type: application/json');
        $controller->webhook();
        exit;
    }

    if (!isset($_SESSION['usuario_id']) || !$_SESSION['usuario_id']) {
        header('Location: /index.php');
        exit();
    }

    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    $metodo = $_SERVER['REQUEST_METHOD'];

    if ($metodo === 'POST') {
        $controller = new PagamentoController();
        
        if (isset($_GET['route']) && $_GET['route'] === 'criar_checkout') {
            header('Content-Type: application/json');
            $controller->criarCheckout();
            exit;
        }
    }

    if ($metodo === 'GET') {
        $controller = new PagamentoController();
        
        if (isset($_GET['route']) && $_GET['route'] === 'historico') {
            header('Content-Type: application/json');
            $controller->historicoPagamentos();
            exit;
        }
        
        if (isset($_GET['route']) && $_GET['route'] === 'status' && isset($_GET['produto_id'])) {
            header('Content-Type: application/json');
            $controller->verificarStatus($_GET['produto_id']);
            exit;
        }
    }
?>