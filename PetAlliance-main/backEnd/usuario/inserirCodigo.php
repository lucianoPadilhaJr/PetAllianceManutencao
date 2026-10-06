<?php

session_start();

require_once __DIR__ . "/../../backEnd/config/config.php";
require_once __DIR__ . "/../../backEnd/models/usuario.php";
require_once __DIR__ . "/../../backEnd/models/usuarioDAO.php";
require_once __DIR__ . "/../../backEnd/models/verificacaoDAO.php";
require_once __DIR__ . "/../../backEnd/models/EmailService.php";
require_once __DIR__ . "/../../backEnd/controllers/api/usuarioController.php";

$controller = new UsuarioController();

// GET ?token=xxx â€” veio por link, jÃ¡ verifica e redireciona
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['token'])) {
    $resultado = $controller->verificarToken($_GET['token']);
    if ($resultado['success'] && $resultado['tipo'] === 'recuperacao') {
        $_SESSION['recuperacao_autorizado'] = $resultado['usuario_id'];
        header("Location: /backEnd/usuario/redefinirSenha.php");
        exit();
    } else {
        header("Location: /backEnd/usuario/esqueceuSenha.php?erro=" . urlencode($resultado['message']));
        exit();
    }
}

// POST â€” rotas AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    $route = $_GET['route'] ?? '';

    // Verificar cÃ³digo
    if ($route === 'verificar_codigo') {
        $usuarioId = $_POST['usuario_id'] ?? $_SESSION['recuperacao_usuario_id'] ?? null;
        $codigo = trim($_POST['codigo'] ?? '');

        if (!$usuarioId || !$codigo) {
            echo json_encode(["success" => false, "message" => "Dados incompletos."]);
            exit();
        }

        $resultado = $controller->verificarCodigo($usuarioId, $codigo, 'recuperacao');
        if ($resultado['success']) {
            $_SESSION['recuperacao_autorizado'] = $usuarioId;
        }
        echo json_encode($resultado);
        exit();
    }

    // Reenviar cÃ³digo
    if ($route === 'reenviar') {
        $usuarioId = $_SESSION['recuperacao_usuario_id'] ?? null;
        if (!$usuarioId) {
            echo json_encode(["success" => false, "message" => "UsuÃ¡rio nÃ£o identificado."]);
            exit();
        }
        $usuario = (new UsuarioDAO())->readPorId($usuarioId);
        if (!$usuario) {
            echo json_encode(["success" => false, "message" => "UsuÃ¡rio nÃ£o encontrado."]);
            exit();
        }
        $enviou = $controller->enviarRecuperacaoSenha($usuario->getEmail());
        echo json_encode($enviou
            ? ["success" => true, "message" => "Email reenviado! Verifique sua caixa de entrada."]
            : ["success" => false, "message" => "Erro ao enviar email. Tente novamente."]
        );
        exit();
    }

    echo json_encode(["success" => false, "message" => "Rota invÃ¡lida."]);
    exit();
}

// Exibir pÃ¡gina
$usuarioId = $_SESSION['recuperacao_usuario_id'] ?? null;
if (!$usuarioId) {
    header("Location: /backEnd/usuario/esqueceuSenha.php");
    exit();
}

require __DIR__ . "/../../frontEnd/views/inserirCodigo.html";
require __DIR__ . "/../../frontEnd/views/footer.html";
