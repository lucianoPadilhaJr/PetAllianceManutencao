<?php
    require_once __DIR__ . '/../../backEnd/config/config.php';
    require_once __DIR__ . '/../../backEnd/models/usuario.php';
    require_once __DIR__ . '/../../backEnd/models/usuarioDAO.php';
    require_once __DIR__ . '/../../backEnd/models/verificacaoDAO.php';
    require_once __DIR__ . '/../../backEnd/models/EmailService.php';

    session_start();

    if(!$_SESSION['usuario_id']) {
        header('Location: /index.php');
        exit();
    }

    $usuarioDAO = new UsuarioDAO();
    $verificacaoDAO = new VerificacaoDAO();
    $cpf = preg_replace('/[^0-9]/', '', $_SESSION['usuario_cpf'] ?? '');
    $usuarioAtual = $usuarioDAO->read($cpf);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $rota = $_POST['route'] ?? '';

        // ETAPA 1: confirmar senha + enviar cÃ³digo pro email atual
        if ($rota === 'confirmar_senha') {
            $senha = $_POST['senha'] ?? '';
            $novoEmail = $_POST['novo_email'] ?? '';

            $senhaCorreta = password_verify($senha, $usuarioAtual->getSenha());
            if (!$senhaCorreta) {
                header("Location: /backEnd/usuario/alterarEmail.php?erro=senha_incorreta");
                exit();
            }

            if (empty($novoEmail) || !filter_var($novoEmail, FILTER_VALIDATE_EMAIL)) {
                header("Location: /backEnd/usuario/alterarEmail.php?erro=email_invalido");
                exit();
            }

            if ($novoEmail === $usuarioAtual->getEmail()) {
                header("Location: /backEnd/usuario/alterarEmail.php?erro=email_igual");
                exit();
            }

            $_SESSION['novo_email_pendente'] = $novoEmail;

            $token = bin2hex(random_bytes(32));
            $codigo = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $expiracao = date('Y-m-d H:i:s', strtotime('+30 minutes'));

            $verificacaoDAO->limparPorUsuario($usuarioAtual->getId(), 'alteracao_email_atual');
            $verificacaoDAO->salvar($usuarioAtual->getId(), $token, $codigo, $expiracao, 'alteracao_email_atual');

            $emailService = new EmailService();
            $emailService->enviarVerificacao($usuarioAtual->getEmail(), $usuarioAtual->getNome(), $token, $codigo);

            $_SESSION['etapa'] = 'codigo_atual';
            header("Location: /backEnd/usuario/alterarEmail.php");
            exit();
        }

        // ETAPA 2: verificar cÃ³digo do email atual
        if ($rota === 'verificar_codigo_atual') {
            $codigo = $_POST['codigo'] ?? '';

            $dados = $verificacaoDAO->buscarPorUsuario($usuarioAtual->getId(), 'alteracao_email_atual');

            if ($dados && $dados['codigo'] === $codigo) {
                $verificacaoDAO->marcarUsado($dados['id']);
                $verificacaoDAO->limparPorUsuario($usuarioAtual->getId(), 'alteracao_email_atual');

                $novoEmail = $_SESSION['novo_email_pendente'] ?? '';

                $token = bin2hex(random_bytes(32));
                $codigoNovo = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
                $expiracao = date('Y-m-d H:i:s', strtotime('+30 minutes'));

                $verificacaoDAO->limparPorUsuario($usuarioAtual->getId(), 'alteracao_email_novo');
                $verificacaoDAO->salvar($usuarioAtual->getId(), $token, $codigoNovo, $expiracao, 'alteracao_email_novo');

                $emailService = new EmailService();
                $emailService->enviarVerificacao($novoEmail, $usuarioAtual->getNome(), $token, $codigoNovo);

                $_SESSION['etapa'] = 'codigo_novo';
                header("Location: /backEnd/usuario/alterarEmail.php");
                exit();
            } else {
                header("Location: /backEnd/usuario/alterarEmail.php?erro=codigo_invalido");
                exit();
            }
        }

        // ETAPA 3: verificar cÃ³digo do novo email e salvar
        if ($rota === 'verificar_codigo_novo') {
            $codigo = $_POST['codigo'] ?? '';

            $dados = $verificacaoDAO->buscarPorUsuario($usuarioAtual->getId(), 'alteracao_email_novo');

            if ($dados && $dados['codigo'] === $codigo) {
                $verificacaoDAO->marcarUsado($dados['id']);
                $verificacaoDAO->limparPorUsuario($usuarioAtual->getId(), 'alteracao_email_novo');

                $novoEmail = $_SESSION['novo_email_pendente'] ?? '';
                if (!empty($novoEmail)) {
                    $usuarioAtual->setEmail($novoEmail);
                    $usuarioDAO->updateUsuario($usuarioAtual, $cpf);
                    $_SESSION['usuario_email'] = $novoEmail;
                    unset($_SESSION['novo_email_pendente']);
                    unset($_SESSION['etapa']);
                }

                header("Location: /backEnd/usuario/alterarEmail.php?sucesso=1");
                exit();
            } else {
                header("Location: /backEnd/usuario/alterarEmail.php?erro=codigo_invalido_novo");
                exit();
            }
        }

        // Reenviar cÃ³digo
        if ($rota === 'reenviar') {
            $tipo = $_POST['tipo'] ?? '';
            $email = $tipo === 'novo' ? ($_SESSION['novo_email_pendente'] ?? '') : $usuarioAtual->getEmail();

            $token = bin2hex(random_bytes(32));
            $codigo = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $expiracao = date('Y-m-d H:i:s', strtotime('+30 minutes'));

            $tipoBanco = $tipo === 'novo' ? 'alteracao_email_novo' : 'alteracao_email_atual';
            $verificacaoDAO->limparPorUsuario($usuarioAtual->getId(), $tipoBanco);
            $verificacaoDAO->salvar($usuarioAtual->getId(), $token, $codigo, $expiracao, $tipoBanco);

            $emailService = new EmailService();
            $emailService->enviarVerificacao($email, $usuarioAtual->getNome(), $token, $codigo);

            header("Location: /backEnd/usuario/alterarEmail.php?reenviado=1");
            exit();
        }
    }

    $etapa = $_SESSION['etapa'] ?? 'dados';
    $erro = $_GET['erro'] ?? '';
    $sucesso = $_GET['sucesso'] ?? '';

    require __DIR__ . "/../views/navBar.php";
    require __DIR__ . "/../../frontEnd/views/alterarEmail.html";