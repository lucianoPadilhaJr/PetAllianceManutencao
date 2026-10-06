<?php

require_once __DIR__ . "/../models/denunciaDAO.php";
require_once __DIR__ . "/../models/denuncia.php";
require_once __DIR__ . "/../controllers/api/denunciaController.php";

session_start();

if(!$_SESSION['usuario_id']) {
    header('Location: /index.php');
    exit();
}

$erro = '';
$sucesso = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        $usuario_id = $_SESSION["usuario_id"];
        $descricao = trim($_POST["descricao"] ?? "");
        $tipo_alvo = $_POST["tipo_alvo"] ?? $_GET["tipo"] ?? "";
        $alvo_id = $_POST["alvo_id"] ?? $_GET["id"] ?? null;
        $resolvido = 0;

        if (empty($descricao)) {
            throw new InvalidArgumentException("Descreva o motivo da denÃºncia.");
        }

        $controller = new DenunciaController();
        $controller->criarDenuncia(new Denuncia($usuario_id, $tipo_alvo, $descricao, $alvo_id, $resolvido));

        $sucesso = true;
    } catch (InvalidArgumentException $e) {
        $erro = $e->getMessage();
    } catch (PDOException $e) {
        $erro = "Erro ao salvar denÃºncia.";
    }
}

require __DIR__ . "/../../frontEnd/views/cadastrarDenuncia.html";