<?php
    require_once __DIR__ . '/../../backEnd/models/usuarioDAO.php';
    require_once __DIR__ . '/../../backEnd/models/usuario.php';

    session_start();

    if(!$_SESSION['usuario_id']) {
        header('Location: /index.php');
        exit();
    }

    $dao = new UsuarioDAO();
    $cpf = preg_replace('/[^0-9]/', '', $_SESSION['usuario_cpf'] ?? '');
    $usuarioAtual = $dao->read($cpf);

    $sucesso = $_GET['sucesso'] ?? '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            $nome = $_POST['nome'] ?? '';
            $cep = $_POST['cep'] ?? '';

            if (empty($nome)) {
                throw new InvalidArgumentException("Nome Ã© obrigatÃ³rio.");
            }

            $usuarioAtual->setNome($nome);
            $usuarioAtual->setCep(preg_replace('/[^0-9]/', '', $cep));

            // Upload de foto
            if (isset($_FILES['imagemPerfil']) && $_FILES['imagemPerfil']['error'] !== UPLOAD_ERR_NO_FILE) {
                if ($_FILES['imagemPerfil']['error'] !== UPLOAD_ERR_OK) {
                    throw new InvalidArgumentException("Erro no upload da imagem.");
                }
                if ($_FILES['imagemPerfil']['size'] > MAX_FILE_SIZE) {
                    throw new InvalidArgumentException("Arquivo muito grande. Tamanho mÃ¡ximo permitido: 100MB.");
                }

                $extensao = pathinfo($_FILES['imagemPerfil']['name'], PATHINFO_EXTENSION);
                $permitidos = ['jpg', 'jpeg', 'png', 'webp'];
                if (!in_array(strtolower($extensao), $permitidos)) {
                    throw new InvalidArgumentException("Tipo de imagem nÃ£o permitido. Use JPG, PNG ou WEBP.");
                }

                $fotoAntiga = $usuarioAtual->getImagem();
                $novoNome = uniqid('foto_') . '.' . $extensao;
                $caminho = __DIR__ . '/../../uploads/usuario/' . $novoNome;

                if (move_uploaded_file($_FILES['imagemPerfil']['tmp_name'], $caminho)) {
                    if ($fotoAntiga !== "placeholder.webp" && file_exists(__DIR__ . '/../../uploads/usuario/' . $fotoAntiga)) {
                        unlink(__DIR__ . '/../../uploads/usuario/' . $fotoAntiga);
                    }
                    $usuarioAtual->setImagem($novoNome);
                    $_SESSION['usuario_imagem'] = $novoNome;
                } else {
                    throw new Exception("Erro ao salvar a imagem.");
                }
            }

            $dao->updateUsuario($usuarioAtual, $cpf);
            $_SESSION['usuario_nome'] = $nome;

            header("Location: /backEnd/usuario/editarPerfil.php?sucesso=1");
            exit();
        } catch (InvalidArgumentException $e) {
            $erro = $e->getMessage();
        } catch (Exception $e) {
            $erro = "Erro: " . $e->getMessage();
        }
    }

    require __DIR__ . "/../views/navBar.php";
    require __DIR__ . "/../../frontEnd/views/editarPerfil.html";