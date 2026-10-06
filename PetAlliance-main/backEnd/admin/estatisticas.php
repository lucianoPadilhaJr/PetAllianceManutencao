<?php
require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/../models/usuarioDAO.php";
require_once __DIR__ . "/../models/usuario.php";

session_start();

if (!isset($_SESSION['usuario_cpf'])) {
    die("UsuÃ¡rio nÃ£o estÃ¡ logado.");
}

$usuarioDAO = new UsuarioDAO();
$cpf = $_SESSION['usuario_cpf'];
$usuario = $usuarioDAO->read($cpf);
$ehAdmin = $usuario && $usuario->getTipo() == 1;

if (!$ehAdmin) {
    header("Location: /backEnd/home.php?erro=acesso_negado");
    exit;
}

$metodo = $_SERVER['REQUEST_METHOD'];

if ($metodo === 'GET' && isset($_GET['route']) && $_GET['route'] === 'sistema') {
    header('Content-Type: application/json');
    try {
        $pdo = Conexao::getConexao();
        $totalUsuarios = $pdo->query("SELECT COUNT(*) FROM tb_usuarios")->fetchColumn();
        $totalAnimais = $pdo->query("SELECT COUNT(*) FROM tb_pets")->fetchColumn();
        $totalDenuncias = $pdo->query("SELECT COUNT(*) FROM tb_denuncias")->fetchColumn();
        echo json_encode([
            'sucesso' => true,
            'sistema' => [
                'totalUsuarios' => (int) $totalUsuarios,
                'totalAnimais' => (int) $totalAnimais,
                'totalDenuncias' => (int) $totalDenuncias,
            ]
        ]);
    } catch (Exception $e) {
        echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
    }
    exit;
}

require __DIR__ . "/../views/navBar.php";
require __DIR__ . "/../../frontEnd/views/adminEstatisticas.html";
require __DIR__ . "/../../frontEnd/views/footer.html";