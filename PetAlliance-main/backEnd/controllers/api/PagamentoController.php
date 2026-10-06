<?php
    require_once __DIR__ . "/../../config/config.php";
    require_once __DIR__ . "/../../models/PagamentoAbacatePay.php";

    class PagamentoController {
        private $pagamentoService;

        public function __construct() {
            $this->pagamentoService = new PagamentoAbacatePay();
        }

        public function criarCheckout() {
            $dados = json_decode(file_get_contents("php://input"), true);
            
            $animalId = $dados['animal_id'] ?? null;
            $produtoId = $dados['product_id'] ?? null;
            $preco = $dados['preco'] ?? null;
            $usuarioId = $_SESSION['usuario_id'] ?? null;

            if ((!$animalId && !$produtoId) || !$preco || !$usuarioId) {
                echo json_encode([
                    'success' => false,
                    'error' => 'Dados incompletos'
                ]);
                return;
            }

            $resultado = $this->pagamentoService->criarCheckout($animalId, $produtoId, $preco, $usuarioId);

            if ($resultado['success'] && isset($resultado['id'])) {
                $_SESSION['ultimo_checkout_id'] = $resultado['id'];
                $_SESSION['ultimo_produto_id'] = $produtoId;
            }

            echo json_encode($resultado);
        }

        public function webhook() {
            $this->pagamentoService->webhookNotificacao();
        }

        public function historicoPagamentos() {
            $usuarioId = $_SESSION['usuario_id'] ?? null;
            
            if (!$usuarioId) {
                echo json_encode([]);
                return;
            }

            $pagamentos = $this->pagamentoService->getPagamentosUsuario($usuarioId);
            echo json_encode($pagamentos);
        }

        public function verificarStatus($produtoId) {
            $pagamento = $this->pagamentoService->verificarStatusPagamento($produtoId);
            echo json_encode($pagamento);
        }
    }