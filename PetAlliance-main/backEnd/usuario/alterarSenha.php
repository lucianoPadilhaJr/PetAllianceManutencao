<?php
    require_once __DIR__ . '/../../backEnd/models/usuarioDAO.php';
    require_once __DIR__ . '/../../backEnd/models/usuario.php';
    require_once __DIR__ . '/../../backEnd/config/validacao.php';

    session_start();

    if(!$_SESSION['usuario_id']) {
        header('Location: /index.php');
        exit();
    }

    $dao = new UsuarioDAO();
    $cpf = preg_replace('/[^0-9]/', '', $_SESSION['usuario_cpf'] ?? '');
    $usuarioAtual = $dao->read($cpf);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $senhaAtual = $_POST['senha_atual'] ?? '';
        $novaSenha = $_POST['nova_senha'] ?? '';
        $confirmarSenha = $_POST['confirmar_senha'] ?? '';

        $senhaCorreta = password_verify($senhaAtual, $usuarioAtual->getSenha());
        if (!$senhaCorreta) {
            $erro = "Senha atual incorreta.";
        } else {
            $validacao = validarSenhaForte($novaSenha);
            if ($validacao !== true) {
                $erro = $validacao;
            } elseif ($novaSenha !== $confirmarSenha) {
                $erro = "A confirmaÃ§Ã£o nÃ£o corresponde Ã  nova senha.";
            } else {
                $dao->updateSenha($usuarioAtual->getId(), $novaSenha);
                $sucesso = true;
            }
        }
    }

    require __DIR__ . "/../views/navBar.php";
    require __DIR__ . "/../../frontEnd/views/alterarSenha.html";