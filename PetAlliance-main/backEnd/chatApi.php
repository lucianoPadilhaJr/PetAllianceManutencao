<?php
require_once __DIR__ . '/../backEnd/controllers/api/chatController.php';

session_start();

header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(['erro' => 'Não autorizado']);
    exit;
}

$metodo = $_SERVER['REQUEST_METHOD'];
$usuarioId = $_SESSION['usuario_id'];

try {
    $controller = new ChatController();

    if ($metodo === 'GET' && isset($_GET['route'])) {
        switch ($_GET['route']) {
            case 'listar_conversas':
                $_GET['usuario_id'] = $usuarioId;
                $controller->listarConversas();
                break;
            case 'listar_mensagens':
                $controller->listarMensagens();
                break;
            case 'novas_mensagens':
                $controller->novasMensagens();
                break;
            case 'dados_conversa':
                $controller->dadosConversa();
                break;
            default:
                http_response_code(404);
                echo json_encode(['erro' => 'Rota não encontrada']);
        }
        exit;
    }

    if ($metodo === 'POST' && isset($_GET['route'])) {
        if ($_GET['route'] === 'enviar') {
            $controller->enviarMensagem();
            exit;
        }
    }

    http_response_code(404);
    echo json_encode(['erro' => 'Rota não encontrada']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['erro' => $e->getMessage()]);
}
