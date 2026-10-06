<?php
    require_once __DIR__ . '/../controllers/api/animalController.php';

    session_start();

    if(!$_SESSION['usuario_id']) {
        header('Location: /index.php');
        exit();
    }

    $usuarioId = $_SESSION['usuario_id'];

    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    $metodo = $_SERVER['REQUEST_METHOD'];
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    if ($metodo === 'GET' && isset($_GET['route']) && $_GET['route'] === 'animais') {
        header('Content-Type: application/json');   
        $controllerAnimal = new AnimalController();
        $controllerAnimal->listarAnimaisFavoritos($usuarioId);
        exit;
    }

    require __DIR__ . "/../views/navBar.php";
    ?><script>window.USUARIO_ID = <?= (int) $usuarioId ?>;</script><?php
    require __DIR__ . "/../../frontEnd/views/favoritos.html";
    require __DIR__ . "/../../frontEnd/views/footer.html";