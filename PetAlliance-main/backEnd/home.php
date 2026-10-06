<?php
    require_once __DIR__ . '/controllers/api/animalController.php';
    require_once __DIR__ . '/controllers/api/MembroController.php';
    require_once __DIR__ . "/models/usuarioDAO.php";

    session_start();

    if(!isset($_SESSION['usuario_id']) || !$_SESSION['usuario_id']) {
        header('Location: /index.php');
        exit();
    }

    $metodo = $_SERVER['REQUEST_METHOD'];
    $usuarioId = $_SESSION['usuario_id'];

    $usuarioDAO = new UsuarioDAO();

    if (!isset($_SESSION['usuario_cpf'])) {
        die("Usuário não está logado.");
    }

    $cpf = $_SESSION['usuario_cpf'];
    $usuario = $usuarioDAO->read($cpf);

    $ehAdmin = $usuario && $usuario->getTipo() == 1;

    // Verificar pagamento pendente ao retornar do AbacatePay
    $checkoutIdPendente = $_SESSION['ultimo_checkout_id'] ?? null;
    $produtoIdPendente = $_SESSION['ultimo_produto_id'] ?? null;
    unset($_SESSION['ultimo_checkout_id'], $_SESSION['ultimo_produto_id']);

    if ($checkoutIdPendente && $produtoIdPendente) {
        require_once __DIR__ . '/../backEnd/models/PagamentoAbacatePay.php';
        $pagamentoService = new PagamentoAbacatePay();

        $statusApi = $pagamentoService->consultarStatusApiAbacatePay($checkoutIdPendente);

        if ($statusApi) {
            $statusApiValue = $statusApi['status'] ?? 'PENDING';

            if ($statusApiValue === 'PAID') {
                $transacaoId = $statusApi['transactionId'] ?? $statusApi['transaction_id'] ?? null;
                $pagamentoService->atualizarStatusPagamento($checkoutIdPendente, 'PAID', $transacaoId);
                $_SESSION['flash'] = [
                    'tipo' => 'sucesso',
                    'mensagem' => 'Pagamento confirmado! Seja bem-vindo como membro.'
                ];
            }
        }
    }

    try {
        if ($metodo === 'GET' && isset($_GET['route']) && $_GET['route'] === 'buscar_animais') {
            header('Content-Type: application/json');
            $controllerAnimal = new AnimalController();
            $termo = $_GET['termo'] ?? '';
            $filtros = [];
            foreach (['porte', 'cor', 'tipo', 'vacinado', 'certificado'] as $f) {
                if (!empty($_GET[$f])) {
                    $filtros[$f] = $_GET[$f];
                }
            }
            $pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
            $controllerAnimal->buscarAnimais($termo, $usuarioId, $filtros, $pagina);
            exit;
        }

        if ($metodo === 'GET' && isset($_GET['route']) && $_GET['route'] === 'animais') {
            header('Content-Type: application/json');
            $controllerAnimal = new AnimalController();
            $pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
            $controllerAnimal->listarAnimais($usuarioId, $pagina);
            exit;
        }

        if ($metodo === 'GET' && isset($_GET['route']) && $_GET['route'] === 'detalhes_animal' && isset($_GET['id'])) {
            header('Content-Type: application/json');
            $controllerAnimal = new AnimalController();
            $controllerAnimal->read($_GET['id'], $usuarioId);
            exit;
        }

        if ($metodo === 'GET' && isset($_GET['route']) && $_GET['route'] === 'favoritar_animal' && isset($_GET['idAnimal'])) {
            header('Content-Type: application/json');
            $controllerAnimal = new AnimalController();
            $controllerAnimal->favoritarAnimal($usuarioId, $_GET['idAnimal']);
            exit;
        }

        if ($metodo === 'GET' && isset($_GET['route']) && $_GET['route'] === 'animais_destaque') {
            header('Content-Type: application/json');
            $controllerMembro = new MembroController();
            $controllerMembro->jsonAnimaisDestaque();
            exit;
        }

        if ($metodo === 'GET' && isset($_GET['route']) && $_GET['route'] === 'meus_destaques_ids') {
            header('Content-Type: application/json');
            $controllerMembro = new MembroController();
            $destaques = $controllerMembro->listarMeusDestaques($usuarioId);
            $ids = array_map(function($a) { return (int)$a['id']; }, $destaques);
            echo json_encode($ids);
            exit;
        }

        if ($metodo === 'POST' && isset($_GET['route']) && $_GET['route'] === 'destacar_animal') {
            header('Content-Type: application/json');
            $dados = json_decode(file_get_contents("php://input"), true);
            $animalId = $dados['animal_id'] ?? null;
            if (!$animalId) {
                echo json_encode(['success' => false, 'error' => 'animal_id Ã© obrigatÃ³rio.']);
                exit;
            }
            $controllerMembro = new MembroController();
            $controllerMembro->destacarAnimal($usuarioId, $animalId);
            exit;
        }

        if ($metodo === 'POST' && isset($_GET['route']) && $_GET['route'] === 'remover_destaque') {
            header('Content-Type: application/json');
            $dados = json_decode(file_get_contents("php://input"), true);
            $animalId = $dados['animal_id'] ?? null;
            if (!$animalId) {
                echo json_encode(['success' => false, 'error' => 'animal_id Ã© obrigatÃ³rio.']);
                exit;
            }
            $controllerMembro = new MembroController();
            $controllerMembro->removerDestaque($usuarioId, $animalId);
            exit;
        }

        if ($metodo === 'POST' && isset($_GET['route']) && $_GET['route'] === 'remover_foto') {
            header('Content-Type: application/json');
            $dados = json_decode(file_get_contents("php://input"), true);
            $petId = $dados['pet_id'] ?? null;
            $fotoPath = $dados['foto_path'] ?? null;
            if (!$petId || !$fotoPath) {
                echo json_encode(['success' => false, 'error' => 'Dados incompletos.']);
                exit;
            }
            require_once __DIR__ . '/../backEnd/models/animalDAO.php';
            $dao = new AnimalDAO();
            $resultado = $dao->removerFotoByPath($petId, $fotoPath);
            echo json_encode(['success' => $resultado, 'error' => $resultado ? null : 'Foto nÃ£o encontrada.']);
            exit;
        }
    } catch (Exception $e) {
        header('Content-Type: application/json');
        http_response_code(500);
        echo json_encode(["erro" => $e->getMessage()]);
        exit;
    }

    $flashMessage = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);

    if ($flashMessage){
        $jsonMsg = json_encode($flashMessage, JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT);
        echo '<script>window.flashMessage = ' . $jsonMsg . ';</script>';
        echo '<script>
            document.addEventListener("DOMContentLoaded", function() {
                var msg = ' . $jsonMsg . ';
                var toast = document.createElement("div");
                toast.className = "toast-message toast-" + (msg.tipo || "info");
                toast.textContent = msg.mensagem || "";
                document.body.appendChild(toast);
                setTimeout(function() { toast.remove(); }, 4000);
            });
        </script>';
    }
    try {
        if ($metodo === 'DELETE' && isset($_GET['route']) && $_GET['route'] === 'excluir_animal' && isset($_GET['id'])) {
            header('Content-Type: application/json');
            $controllerAnimal = new AnimalController();
            $controllerAnimal->deletarAnimal($_GET['id']);
            exit;
        }
    } catch (Exception $e) {
        header('Content-Type: application/json');
        http_response_code(500);
        echo json_encode(["erro" => $e->getMessage()]);
        exit;
    }

    require __DIR__ . '/views/navBar.php'; ?>
    <script>
        window.EH_ADMIN = <?= $ehAdmin ? 'true' : 'false' ?>;
        window.USUARIO_ID = <?= (int) $usuarioId ?>;
    </script>
    <?php require __DIR__ . '/../frontEnd/views/destaques.html'; ?>
    <script src="/frontEnd/utils/destaques.js"></script>
    <?php
    require __DIR__ . '/../frontEnd/views/home.html';

    if (isset($_GET['mensagem'])) {
        if ($_GET['mensagem'] === 'animal_cadastrado') {
            echo '<script>
                document.addEventListener("DOMContentLoaded", function() {
                    var toast = document.createElement("div");
                    toast.className = "toast-message toast-sucesso";
                    toast.textContent = "Animal cadastrado com sucesso";
                    document.body.appendChild(toast);
                    setTimeout(function() { toast.remove(); }, 4000);
                });
            </script>';
        } else {
             echo "<p id='mensagem-erro' class='erro-escondido'>mensagem de erro</p>";
        }
    }
    if (isset($_GET['erro'])) {
        if ($_GET['erro'] === 'acesso_negado') {
            echo '<script>
                document.addEventListener("DOMContentLoaded", function() {
                    var toast = document.createElement("div");
                    toast.className = "toast-message toast-erro";
                    toast.textContent = "VocÃª nÃ£o pode entrar aqui";
                    document.body.appendChild(toast);
                    setTimeout(function() { toast.remove(); }, 4000);
                });
            </script>';
        } else {
            echo '<script>
                document.addEventListener("DOMContentLoaded", function() {
                    var toast = document.createElement("div");
                    toast.className = "toast-message toast-erro";
                    toast.textContent = "Ocorreu um erro";
                    document.body.appendChild(toast);
                    setTimeout(function() { toast.remove(); }, 4000);
                });
            </script>';
        }
    }
    require __DIR__ . '/../frontEnd/views/footer.html';
?>

