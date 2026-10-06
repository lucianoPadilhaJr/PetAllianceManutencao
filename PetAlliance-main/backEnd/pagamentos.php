<?php
    require_once __DIR__ . "/config/config.php";
    require_once __DIR__ . "/controllers/api/PagamentoController.php";

    session_start();

    if(!isset($_SESSION['usuario_id']) || !$_SESSION['usuario_id']) {
        header('Location: /index.php');
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['route']) && $_GET['route'] === 'historico') {
        header('Content-Type: application/json');
        $controller = new PagamentoController();
        $controller->historicoPagamentos();
        exit;
    }

    require __DIR__ . "/views/navBar.php";
    require __DIR__ . "/../frontEnd/views/pagamentos.html";
    require __DIR__ . "/../frontEnd/views/footer.html";