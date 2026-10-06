<?php
    require_once __DIR__ . '/../../backEnd/controllers/api/usuarioController.php';

    session_start();

    if(!$_SESSION['usuario_id']) {
        header('Location: /index.php');
        exit();
    }

    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    $metodo = $_SERVER['REQUEST_METHOD'];
    $cpf = isset($_SESSION['usuario_cpf']) ? preg_replace('/[^0-9]/', '', $_SESSION['usuario_cpf']) : null;

    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    $acceptHeader = $_SERVER['HTTP_ACCEPT'] ?? '';
    $wantsJson = (isset($_GET['route']) && $_GET['route'] === 'usuario') || $isAjax || (strpos($acceptHeader, 'application/json') !== false);

    if ($metodo === 'GET' && $wantsJson && isset($cpf)) {
        header('Content-Type: application/json');
        $controllerUsuario = new usuarioController();
        $controllerUsuario->read($cpf);
        exit;
    }

    require __DIR__ . "/../views/navBar.php";
    require __DIR__ . "/../../frontEnd/views/perfil.html";
    require __DIR__ . "/../../frontEnd/views/footer.html";