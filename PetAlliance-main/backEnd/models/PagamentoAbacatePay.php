<?php
    require_once __DIR__ . "/../../backEnd/config/config.php";

    class PagamentoAbacatePay {
        private $pdo;
        private $apiKey;
        private $apiUrl;

        public function __construct() {
            $this->pdo = Conexao::getConexao();
            $this->apiKey = getenv('ABACATEPAY_API_KEY');
            if (!$this->apiKey) {
                $this->apiKey = $_ENV['ABACATEPAY_API_KEY'] ?? '';
            }
            $this->apiUrl = getenv('ABACATEPAY_URL');
            if (!$this->apiUrl) {
                $this->apiUrl = $_ENV['ABACATEPAY_URL'] ?? 'https://api.sandbox.abacatepay.com';
            }
        }

        public function criarCheckout($animalId, $produtoId, $preco, $usuarioId) {
            try {
                $dadosEnvio = $this->montarDadosCheckout($animalId, $produtoId, $preco, $usuarioId);

                $respostaApi = $this->enviarRequisicaoCheckout($dadosEnvio);
                $dados = json_decode($respostaApi, true);

                if (!$dados || isset($dados['error'])) {
                    throw new Exception($dados['error']['message'] ?? 'Erro na criação do checkout');
                }

                $checkoutData = $dados['data'] ?? $dados;
                $checkoutId = $checkoutData['id'] ?? null;
                $checkoutUrl = $checkoutData['url'] ?? $checkoutData['checkout_url'] ?? null;

                if (!$checkoutUrl) {
                    throw new Exception('Resposta inválida da API: URL de checkout não encontrada');
                }

                $this->registrarPagamento($usuarioId, $produtoId, $checkoutId, $preco);

                return [
                    'success' => true,
                    'checkout_url' => $checkoutUrl,
                    'id' => $checkoutId,
                    'product_id' => $produtoId
                ];
            } catch (Exception $e) {
                return [
                    'success' => false,
                    'error' => $e->getMessage()
                ];
            }
        }

        private function montarDadosCheckout($animalId, $produtoId, $preco, $usuarioId) {
            $protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $dominio = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $baseUrl = "$protocolo://$dominio";

            $dados = [
                'methods' => ['CARD'],
                'returnUrl' => "$baseUrl/backEnd/home.php",
                'completionUrl' => "$baseUrl/backEnd/home.php",
                'items' => [
                    [
                        'id' => (string)$produtoId,
                        'quantity' => 1
                    ]
                ]
            ];

            return $dados;
        }

        private function enviarRequisicaoCheckout($dados) {
            $url = rtrim($this->apiUrl, '/') . '/v2/checkouts/create';

            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($dados),
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $this->apiKey
                ],
                CURLOPT_TIMEOUT => 30
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if (curl_error($ch)) {
                throw new Exception('Erro de conexão com AbacatePay: ' . curl_error($ch));
            }

            curl_close($ch);

            if ($httpCode >= 400) {
                $erro = json_decode($response, true);
                $mensagem = $erro['error'] ?? $erro['message'] ?? 'Erro HTTP ' . $httpCode;
                throw new Exception('AbacatePay retornou erro: ' . $mensagem);
            }

            return $response;
        }

        private function getAnimalById($animalId) {
            $sql = "SELECT * FROM tb_pets WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$animalId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        private function registrarPagamento($usuarioId, $produtoId, $checkoutId, $preco) {
            $planoNome = null;
            $planoMax = null;

            $planosFile = __DIR__ . '/../config/planos.php';
            if (file_exists($planosFile)) {
                $PLANOS = [];
                require $planosFile;
                if (isset($PLANOS[$produtoId])) {
                    $planoNome = $PLANOS[$produtoId]['nome'] ?? null;
                    $planoMax = $PLANOS[$produtoId]['max_destaques'] ?? null;
                }
            }

            $sql = "INSERT INTO tb_pagamentos (usuario_id, prod_id_abacatepay, id_transacao_abacatepay, status_pagamento, valor, plano_nome, plano_max_destaques) VALUES (?, ?, ?, 'PENDING', ?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$usuarioId, $produtoId, $checkoutId, $preco, $planoNome, $planoMax]);
        }

        public function verificarStatusPagamento($produtoId) {
            $sql = "SELECT * FROM tb_pagamentos WHERE prod_id_abacatepay = ? ORDER BY criado_em DESC LIMIT 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$produtoId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        public function atualizarStatusPagamento($checkoutId, $status, $transacaoId = null) {
            $sql = "UPDATE tb_pagamentos SET status_pagamento = ?, id_transacao_abacatepay = COALESCE(?, id_transacao_abacatepay), atualizado_em = NOW() WHERE id_transacao_abacatepay = ? OR prod_id_abacatepay = ?";
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([$status, $transacaoId, $checkoutId, $checkoutId]);

            if ($status === 'PAID') {
                $sqlUpdate = "UPDATE tb_pagamentos SET plano_expiracao = DATE_ADD(NOW(), INTERVAL 1 MONTH) WHERE (id_transacao_abacatepay = ? OR prod_id_abacatepay = ?) AND plano_expiracao IS NULL";
                $stmtUpdate = $this->pdo->prepare($sqlUpdate);
                $stmtUpdate->execute([$checkoutId, $checkoutId]);
            }

            return $result;
        }

        public function webhookNotificacao() {
            $payload = file_get_contents('php://input');
            $dados = json_decode($payload, true);

            if (!$dados || !isset($dados['event'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Payload inválido']);
                exit;
            }

            $evento = $dados['event'];
            $checkoutId = $dados['data']['id'] ?? $dados['data']['checkout_id'] ?? null;
            $transacaoId = $dados['data']['transaction_id'] ?? $dados['data']['id'] ?? null;

            $mapStatus = [
                'checkout.pending' => 'PENDING',
                'checkout.completed' => 'PAID',
                'checkout.expired' => 'EXPIRED',
                'checkout.refunded' => 'REFUNDED',
                'checkout.cancelled' => 'CANCELLED',
                'order.paid' => 'PAID',
                'order.completed' => 'PAID',
                'order.refunded' => 'REFUNDED',
                'order.cancelled' => 'CANCELLED',
                'order.expired' => 'EXPIRED'
            ];

            $status = $mapStatus[$evento] ?? null;

            if ($status && $checkoutId) {
                $this->atualizarStatusPagamento($checkoutId, $status, $transacaoId);
            }

            http_response_code(200);
            echo json_encode(['received' => true]);
        }

        public function getPagamentosUsuario($usuarioId) {
            $sql = "SELECT * FROM tb_pagamentos WHERE usuario_id = ? ORDER BY criado_em DESC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$usuarioId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function consultarStatusApiAbacatePay($checkoutId) {
            $url = rtrim($this->apiUrl, '/') . '/v2/checkouts/get?id=' . urlencode($checkoutId);

            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPGET => true,
                CURLOPT_HTTPHEADER => [
                    'Authorization: Bearer ' . $this->apiKey
                ],
                CURLOPT_TIMEOUT => 15
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if (curl_error($ch)) {
                curl_close($ch);
                return null;
            }

            curl_close($ch);

            if ($httpCode !== 200) {
                return null;
            }

            $dados = json_decode($response, true);
            $checkoutData = $dados['data'] ?? null;

            if (!$checkoutData) {
                return null;
            }

            return $checkoutData;
        }

        public function verificarEAtualizarPagamentoPendente($checkoutId, $produtoId) {
            $pagamento = $this->verificarStatusPagamento($produtoId);

            if (!$pagamento || $pagamento['status_pagamento'] === 'PAID') {
                return $pagamento;
            }

            $statusApi = $this->consultarStatusApiAbacatePay($checkoutId);

            if (!$statusApi) {
                return $pagamento;
            }

            $statusApiValue = $statusApi['status'] ?? 'PENDING';

            if ($statusApiValue === 'PAID') {
                $transacaoId = $statusApi['transactionId'] ?? $statusApi['transaction_id'] ?? null;
                $this->atualizarStatusPagamento($checkoutId, 'PAID', $transacaoId);
                $pagamento['status_pagamento'] = 'PAID';
                $pagamento['atualizado_em'] = date('Y-m-d H:i:s');
                if ($transacaoId) {
                    $pagamento['id_transacao_abacatepay'] = $transacaoId;
                }
            }

            return $pagamento;
        }
    }