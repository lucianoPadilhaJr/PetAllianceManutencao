<?php
session_start();

require_once __DIR__ . "/../../backEnd/config/config.php";
require_once __DIR__ . "/../../backEnd/models/usuario.php";
require_once __DIR__ . "/../../backEnd/models/usuarioDAO.php";
require_once __DIR__ . "/../../backEnd/models/verificacaoDAO.php";
require_once __DIR__ . "/../../backEnd/models/EmailService.php";
require_once __DIR__ . "/../../backEnd/controllers/api/usuarioController.php";

$controller = new UsuarioController();
$usuarioDAO = new UsuarioDAO();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    $input = trim($_POST['cpf'] ?? '');

    if (!$input) {
        echo json_encode(["success" => false, "message" => "Digite seu CPF ou email."]);
        exit();
    }

    if (strpos($input, '@') !== false) {
        $usuario = $usuarioDAO->buscarPorEmail($input);
        if (!$usuario) {
            echo json_encode(["success" => false, "message" => "Email nÃ£o cadastrado."]);
            exit();
        }
    } else {
        $cpf = preg_replace('/[^0-9]/', '', $input);
        if (strlen($cpf) !== 11) {
            echo json_encode(["success" => false, "message" => "CPF deve ter 11 dÃ­gitos (recebido: '$input' -> '$cpf', len=" . strlen($cpf) . ")."]);
            exit();
        }
        if (!$usuarioDAO->validaCPF($cpf)) {
            echo json_encode(["success" => false, "message" => "CPF com dÃ­gitos verificadores invÃ¡lidos."]);
            exit();
        }
        $usuario = $usuarioDAO->read($cpf);
        if (!$usuario) {
            echo json_encode(["success" => false, "message" => "CPF nÃ£o cadastrado."]);
            exit();
        }
    }
    $_SESSION['cpf_digitado'] = $input;

    $enviou = $controller->enviarRecuperacaoSenha($usuario->getEmail());
    if ($enviou) {
        $_SESSION['recuperacao_usuario_id'] = $usuario->getId();
        echo json_encode(["success" => true, "message" => "Email enviado! Verifique sua caixa de entrada.", "usuario_id" => $usuario->getId()]);
    } else {
        echo json_encode(["success" => false, "message" => "Erro ao enviar email. Tente novamente."]);
    }
    exit();
}

$cpfSalvo = $_SESSION['cpf_digitado'] ?? '';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Recuperar Senha - PetAlliance</title>
  <link rel="stylesheet" href="/frontEnd/style/style.css">
  <link rel="stylesheet" href="/frontEnd/style/estilos.css">
</head>
<body style="background:var(--bg);margin:0;min-height:100vh">
<?php
require __DIR__ . "/../../frontEnd/views/esqueceuSenha.html";
require __DIR__ . "/../../frontEnd/views/footer.html";
?>
</body>
</html>
