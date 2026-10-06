<?php

session_start();

require_once __DIR__ . "/../../backEnd/config/config.php";
require_once __DIR__ . "/../../backEnd/config/validacao.php";
require_once __DIR__ . "/../../backEnd/models/usuario.php";
require_once __DIR__ . "/../../backEnd/models/usuarioDAO.php";
require_once __DIR__ . "/../../backEnd/controllers/api/usuarioController.php";

$controller = new UsuarioController();
$usuarioDAO = new UsuarioDAO();

// POST â€” salvar nova senha
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    $usuarioId = $_SESSION['recuperacao_autorizado'] ?? null;
    $senha = trim($_POST['senha'] ?? '');
    $confirmar = trim($_POST['confirmar_senha'] ?? '');

    if (!$usuarioId) {
        echo json_encode(["success" => false, "message" => "SessÃ£o invÃ¡lida. Solicite um novo cÃ³digo."]);
        exit();
    }

    if (!$senha || !$confirmar) {
        echo json_encode(["success" => false, "message" => "Preencha todos os campos."]);
        exit();
    }

    if ($senha !== $confirmar) {
        echo json_encode(["success" => false, "message" => "As senhas nÃ£o conferem."]);
        exit();
    }

    $validacao = validarSenhaForte($senha);
    if ($validacao !== true) {
        echo json_encode(["success" => false, "message" => $validacao]);
        exit();
    }

    $resultado = $controller->redefinirSenha($usuarioId, $senha);
    if ($resultado['success']) {
        $usuarioLogado = $usuarioDAO->readPorId($usuarioId);
        if ($usuarioLogado) {
            $_SESSION['usuario_id'] = $usuarioLogado->getId();
            $_SESSION['usuario_nome'] = $usuarioLogado->getNome();
            $_SESSION['usuario_cpf'] = $usuarioLogado->getCpf();
            $_SESSION['usuario_email'] = $usuarioLogado->getEmail();
            $_SESSION['usuario_imagem'] = $usuarioLogado->getImagem();
        }
        unset($_SESSION['recuperacao_autorizado']);
        unset($_SESSION['recuperacao_usuario_id']);
    }
    echo json_encode($resultado);
    exit();
}

// Verificar se estÃ¡ autorizado (veio do cÃ³digo ou token)
$usuarioId = $_SESSION['recuperacao_autorizado'] ?? null;
if (!$usuarioId) {
    header("Location: /backEnd/usuario/esqueceuSenha.php");
    exit();
}

$usuario = $usuarioDAO->readPorId($usuarioId);
if (!$usuario) {
    header("Location: /backEnd/usuario/esqueceuSenha.php");
    exit();
}

$posArroba = strpos($usuario->getEmail(), '@');
$emailOculto = $posArroba !== false
    ? substr($usuario->getEmail(), 0, 3) . '*****' . substr($usuario->getEmail(), $posArroba)
    : substr($usuario->getEmail(), 0, 3) . '*****';

require __DIR__ . "/../../frontEnd/views/redefinirSenha.html";
require __DIR__ . "/../../frontEnd/views/footer.html";
