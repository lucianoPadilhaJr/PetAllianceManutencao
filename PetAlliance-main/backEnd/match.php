<?php
require_once __DIR__ . '/../backEnd/controllers/api/solicitacaoMatchController.php';
require_once __DIR__ . '/../backEnd/controllers/api/notificacaoController.php';
require_once __DIR__ . "/../backEnd/models/usuarioDAO.php";

session_start();

header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if (!isset($_SESSION['usuario_cpf']) || !isset($_SESSION['usuario_id'])) {
    die("UsuÃ¡rio nÃ£o estÃ¡ logado.");
}

$metodo = $_SERVER['REQUEST_METHOD'];
$usuarioId = $_SESSION['usuario_id'];

try {
    if ($metodo === 'GET' && isset($_GET['route'])) {
        if ($_GET['route'] === 'listar_solicitacoes') {
            $controller = new SolicitacaoMatchController();
            $controller->listarSolicitacoes($usuarioId);
            exit;
        }
        if ($_GET['route'] === 'minhas_solicitacoes') {
            $controller = new SolicitacaoMatchController();
            $controller->listarMinhasSolicitacoes($usuarioId);
            exit;
        }
        if ($_GET['route'] === 'notificacoes') {
            $controller = new NotificacaoController();
            $controller->listar($usuarioId);
            exit;
        }
        if ($_GET['route'] === 'naoLidas') {
            header('Content-Type: application/json');
            $dao = new NotificacaoDAO();
            echo json_encode(['naoLidas' => $dao->naoLidas($usuarioId)]);
            exit;
        }
    }

    if ($metodo === 'POST' && isset($_GET['route'])) {
        session_regenerate_id(true);
        if ($_GET['route'] === 'enviar') {
            $controller = new SolicitacaoMatchController();
            $controller->enviarSolicitacao();
            exit;
        }
        if ($_GET['route'] === 'aceitar') {
            $controller = new SolicitacaoMatchController();
            $controller->aceitarSolicitacao();
            exit;
        }
        if ($_GET['route'] === 'recusar') {
            $controller = new SolicitacaoMatchController();
            $controller->recusarSolicitacao();
            exit;
        }
        if ($_GET['route'] === 'marcarLida') {
            $controller = new NotificacaoController();
            $controller->marcarLida();
            exit;
        }
    }
} catch (Exception $e) {
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(["erro" => $e->getMessage()]);
    exit;
}

require __DIR__ . '/views/navBar.php';
require __DIR__ . '/../frontEnd/views/notificacoes.html';
?>
<script>
    window.USUARIO_ID = <?= (int) $usuarioId ?>;
</script>
<?php
require __DIR__ . '/../frontEnd/views/footer.html';
