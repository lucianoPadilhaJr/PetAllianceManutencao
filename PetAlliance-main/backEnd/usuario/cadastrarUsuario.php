<?php

    require_once __DIR__ . "/../../backEnd/models/usuarioDAO.php";
    require_once __DIR__ . "/../../backEnd/models/usuario.php";
    require_once __DIR__ . "/../../backEnd/models/verificacaoDAO.php";
    require_once __DIR__ . "/../../backEnd/models/EmailService.php";
    require_once __DIR__ . "/../../backEnd/config/validacao.php";
    require_once __DIR__ . "/../controllers/api/usuarioController.php";


    session_start();

    require __DIR__ . "/../../frontEnd/views/cadastrarUsuario.html";
    
    if($_SERVER['REQUEST_METHOD'] === "POST") {
        try {
            if (!isset($_POST['aceite_termos'])) {
                throw new InvalidArgumentException("VocÃª precisa aceitar os Termos de Uso e a PolÃ­tica de Privacidade.");
            }

            $nome = trim($_POST['nome'] ?? "");
            $cpf = trim($_POST['cpf'] ?? "");
            $cep = trim($_POST['cep'] ?? "");
            $email = trim($_POST['email'] ?? "");
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new InvalidArgumentException("Email invÃ¡lido.");
            }
            $senha = trim($_POST['senha'] ?? "");
            $tipo = 0;

            $cepNumerico = preg_replace('/[^0-9]/', '', $cep);
            if (strlen($cepNumerico) !== 8) {
                throw new InvalidArgumentException("CEP invÃ¡lido. Informe um CEP com 8 dÃ­gitos.");
            }

            $validacaoSenha = validarSenhaForte($senha);
            if ($validacaoSenha !== true) {
                throw new InvalidArgumentException($validacaoSenha);
            }
            
            $nomeArquivo = "placeholder.webp";

            $controller = new UsuarioController();

            if(isset($_POST['cadastrarFoto'])) {
                if(!isset($_FILES['imagemPerfil']) || $_FILES['imagemPerfil']['error'] === UPLOAD_ERR_NO_FILE) {
                    echo "Selecione uma imagem";
                    exit();
                }
                if ($_FILES['imagemPerfil']['size'] > MAX_FILE_SIZE) {
                    echo "Arquivo muito grande. Tamanho mÃ¡ximo permitido: 100MB.";
                    exit();
                }
                $extensao  = pathinfo($_FILES['imagemPerfil']['name'], PATHINFO_EXTENSION);
                $permitidos = ['jpg', 'jpeg', 'png', 'webp'];
                if (!in_array(strtolower($extensao), $permitidos) || !validarMimeImagem($_FILES['imagemPerfil']['tmp_name'])) {
                    echo 'Tipo de imagem nÃ£o permitido.';
                    exit();
                } else {
                    $nomeArquivo = uniqid('prod_') . '.' . $extensao;
                    move_uploaded_file($_FILES['imagemPerfil']['tmp_name'], '../../uploads/usuario/' . $nomeArquivo);
                    $usuarioCadastrado = $controller->criarUsuario(new Usuario($nomeArquivo, $cpf, $cep,$tipo, $nome, $email, $senha));

                    $usuarioId = $usuarioCadastrado->getId();
                }
            }

            if (!isset($usuarioCadastrado)) {
                $usuarioCadastrado = $controller->criarUsuario(new Usuario($nomeArquivo, $cpf, $cep, $tipo, $nome, $email, $senha));
                $usuarioId = $usuarioCadastrado->getId();
            }

            // Admin (tipo 1) jÃ¡ verificado e logado
            if ($tipo == 1) {
                $dao = new UsuarioDAO();
                $dao->marcarVerificado($usuarioId);
                session_regenerate_id(true);
                $_SESSION['usuario_id'] = $usuarioId;
                $_SESSION['usuario_nome'] = $usuarioCadastrado->getNome();
                $_SESSION['usuario_email'] = $usuarioCadastrado->getEmail();
                $_SESSION['usuario_imagem'] = $usuarioCadastrado->getImagem();
                $_SESSION['usuario_cpf'] = $usuarioCadastrado->getCpf();
                $_SESSION['usuario_tipo'] = $tipo;
                header("Location: /backEnd/home.php");
                exit();
            }

            // UsuÃ¡rio normal: envia verificaÃ§Ã£o e redireciona
            $controller->enviarEmailVerificacao($usuarioId);
            $_SESSION['usuario_verificacao_id'] = $usuarioId;
            header("Location: /backEnd/verificarEmail.php?id=$usuarioId");
            exit();
        } catch (InvalidArgumentException $e) {
            header("Location: /backEnd/usuario/cadastrarUsuario.php?erro=" . urlencode($e->getMessage()));
            exit();
        } catch (PDOException $e) {
            header("Location: /backEnd/usuario/cadastrarUsuario.php?erro=" . urlencode($e->getMessage()));
            exit();
        }
    }
