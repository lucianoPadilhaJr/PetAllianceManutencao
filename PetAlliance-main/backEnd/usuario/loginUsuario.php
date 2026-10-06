<?php
    session_start();

    require_once __DIR__ . "/../../backEnd/models/usuarioDAO.php";
    require_once __DIR__ . "/../../backEnd/controllers/api/usuarioController.php";

    $usuarioDAO = new UsuarioDAO();
    $controller = new UsuarioController();

    if($_SERVER['REQUEST_METHOD'] === "POST") {
        $cpf = preg_replace('/[^0-9]/', '', trim($_POST['cpf']));
        $_SESSION['cpf_digitado'] = $cpf;
        $senha = trim($_POST['senha']);
    
        $usuario = $usuarioDAO->read($cpf);

        if (!$usuario) {
            header("Location: /backEnd/usuario/loginUsuario.php?erro=CPF nÃ£o cadastrado!");
            exit();
        } elseif ($cpf !== $usuario->getCpf()) {
            header("Location: /backEnd/usuario/loginUsuario.php?erro=CPF invÃ¡lido!");
            exit();
        } else {
            $senhaCorreta = password_verify($senha, $usuario->getSenha());

            if (!$senhaCorreta) {
                header("Location: /backEnd/usuario/loginUsuario.php?erro=Senha incorreta!");
                exit();
            } else {
                // Verificar se email foi verificado
                if (!$controller->isVerificado($usuario->getId())) {
                    $_SESSION['usuario_verificacao_id'] = $usuario->getId();
                    $controller->enviarEmailVerificacao($usuario->getId());
                    header("Location: /backEnd/verificarEmail.php?id=" . $usuario->getId());
                    exit();
                }

                session_regenerate_id(true);
                $_SESSION['usuario_id'] = $usuario->getId();
                $_SESSION['usuario_nome'] = $usuario->getNome();
                $_SESSION['usuario_cpf'] = $usuario->getCpf();
                $_SESSION['usuario_email'] = $usuario->getEmail();
                $_SESSION['usuario_imagem'] = $usuario->getImagem();
                $_SESSION['usuario_tipo'] = $usuario->getTipo();
                header("Location: /backEnd/home.php?sucesso=1");
                exit;
            }
        }

    }

    require __DIR__ . "/../../frontEnd/views/login.html";

    if (isset($_SESSION['erro_login'])) {
        unset($_SESSION['erro_login']);
    }


