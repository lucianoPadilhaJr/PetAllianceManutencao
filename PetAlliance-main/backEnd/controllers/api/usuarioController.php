<?php
    require_once __DIR__ . "/../../config/config.php";
    require_once __DIR__ . "/../../config/validacao.php";
    require_once __DIR__ . "/../../models/usuario.php";
    require_once __DIR__ . "/../../models/usuarioDAO.php";
    require_once __DIR__ . "/../../models/verificacaoDAO.php";
    require_once __DIR__ . "/../../models/EmailService.php";

    class UsuarioController {
        private $dao;
        private $verificacaoDao;

        public function __construct() {
            $this->dao = new UsuarioDAO();
            $this->verificacaoDao = new VerificacaoDAO();
        }

        public function read($cpf) {
            $cpf = preg_replace('/[^0-9]/', '', (string) $cpf);
            $usuario = $this->dao->read($cpf);

            if (!$usuario) {
                echo json_encode(["erro" => "Usuário não encontrado."]);
                return;
            }

            echo json_encode([
                "id" => $usuario->getId(),
                "nome" => $usuario->getNome(),
                "email" => $usuario->getEmail(),
                "cpf" => $usuario->getCpf(),
                "cep" => $usuario->getCep(),
                "imagem" => $usuario->getImagem(),
                "tipo" => $usuario->getTipo()
            ]);
        }

        public function updateUsuario($usuario) {
            try {
                $this->dao->updateUsuario($usuario);
                echo json_encode(["success" => true, "message" => "Usuário atualizado com sucesso."]);
            } catch (Exception $e) {
                echo json_encode(["success" => false, "message" => $e->getMessage()]);
            }
        }

        public function updateUsuarioFromRequest($cpf, $data) {
            $usuarioAtual = $this->dao->read($cpf);

            if (!$usuarioAtual) {
                echo json_encode(["success" => false, "message" => "Usuário não encontrado."]);
                return;
            }

            $cpfNovo = !empty($data['cpf']) ? preg_replace('/[^0-9]/', '', $data['cpf']) : $cpf;
            if (!$this->dao->validaCPF($cpfNovo)) {
                echo json_encode(["success" => false, "message" => "CPF inválido."]);
                return;
            }

            $imagem = $usuarioAtual->getImagem();
            $cep = !empty($data['cep']) ? preg_replace('/[^0-9]/', '', $data['cep']) : $usuarioAtual->getCep();
            $nome = !empty($data['nome']) ? trim($data['nome']) : $usuarioAtual->getNome();
            $email = !empty($data['email']) ? trim($data['email']) : $usuarioAtual->getEmail();
            if (!empty($data['email']) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo json_encode(["success" => false, "message" => "Email inválido."]);
                return;
            }
            $senha = $usuarioAtual->getSenha();

            $usuario = new Usuario(
                $imagem,
                $cpfNovo,
                $cep,
                $usuarioAtual->getTipo(),
                $nome,
                $email,
                $senha
            );

            try {
                $this->dao->updateUsuario($usuario, $cpf);
                $_SESSION['usuario_cpf'] = $cpfNovo;
                echo json_encode(["success" => true, "message" => "Usuário atualizado com sucesso."]);
            } catch (Exception $e) {
                echo json_encode(["success" => false, "message" => $e->getMessage()]);
            }
        }
        public function criarUsuario(Usuario $usuario) {
            try {
                return $this->dao->cadastrarUsuario($usuario);
            } catch (InvalidArgumentException $e) {
                throw $e;
            } catch (Exception $e) {
                throw $e;
            }
        }

        public function enviarEmailVerificacao($usuarioId) {
            $usuario = $this->dao->readPorId($usuarioId);
            if (!$usuario) {
                return false;
            }

            $this->verificacaoDao->limparPorUsuario($usuarioId, 'verificacao');

            $token = bin2hex(random_bytes(32));
            $codigo = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $expiracao = date('Y-m-d H:i:s', strtotime('+30 minutes'));

            $this->verificacaoDao->salvar($usuarioId, $token, $codigo, $expiracao, 'verificacao');

            $emailService = new EmailService();
            return $emailService->enviarVerificacao(
                $usuario->getEmail(),
                $usuario->getNome(),
                $token,
                $codigo
            );
        }

        public function verificarToken($token) {
            $pdo = Conexao::getConexao();
            $stmt = $pdo->prepare("SELECT * FROM tb_verificacao_email WHERE token = ? ORDER BY criado_em DESC LIMIT 1");
            $stmt->execute([$token]);
            $dados = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$dados) {
                return ["success" => false, "message" => "Link inválido."];
            }

            if ($dados['usado']) {
                return ["success" => false, "message" => "Este link já foi utilizado."];
            }

            if ($dados['expiracao'] < date('Y-m-d H:i:s')) {
                return ["success" => false, "message" => "Link expirado. Faça login para receber um novo."];
            }

            if ($dados['tipo'] === 'verificacao') {
                $this->dao->marcarVerificado($dados['usuario_id']);
            }

            $this->verificacaoDao->marcarUsado($dados['id']);
            $this->verificacaoDao->limparPorUsuario($dados['usuario_id'], $dados['tipo']);

            if ($dados['tipo'] === 'recuperacao') {
                return ["success" => true, "message" => "Código verificado! Agora crie uma nova senha.", "usuario_id" => $dados['usuario_id'], "tipo" => "recuperacao"];
            }

            return ["success" => true, "message" => "Email verificado com sucesso!", "usuario_id" => $dados['usuario_id'], "tipo" => "verificacao"];
        }

        public function verificarCodigo($usuarioId, $codigo, $tipo = 'verificacao') {
            $dados = $this->verificacaoDao->buscarPorUsuario($usuarioId, $tipo);

            if ($dados && $dados['codigo'] === $codigo) {
                if ($tipo === 'verificacao') {
                    $this->dao->marcarVerificado($usuarioId);
                }
                $this->verificacaoDao->marcarUsado($dados['id']);
                $this->verificacaoDao->limparPorUsuario($usuarioId, $tipo);

                if ($tipo === 'recuperacao') {
                    return ["success" => true, "message" => "Código verificado! Agora crie uma nova senha.", "tipo" => "recuperacao"];
                }

                return ["success" => true, "message" => "Email verificado com sucesso!", "tipo" => "verificacao"];
            }

            $ultimo = $this->verificacaoDao->buscarUltimoPorUsuario($usuarioId, $tipo);
            if (!$ultimo) {
                return ["success" => false, "message" => "Nenhum código encontrado. Solicite um novo."];
            }

            if ($ultimo['expiracao'] < date('Y-m-d H:i:s')) {
                return ["success" => false, "message" => "Código expirado. Solicite um novo."];
            }

            return ["success" => false, "message" => "Código inválido. Verifique e tente novamente."];
        }

        public function reenviarVerificacao($usuarioId) {
            return $this->enviarEmailVerificacao($usuarioId);
        }

        public function isVerificado($usuarioId) {
            $usuario = $this->dao->readPorId($usuarioId);
            if (!$usuario) {
                return false;
            }
            $pdo = Conexao::getConexao();
            $stmt = $pdo->prepare("SELECT verificado FROM tb_usuarios WHERE id = ?");
            $stmt->execute([$usuarioId]);
            return (bool) $stmt->fetchColumn();
        }

        // === RECUPERAÇÃO DE SENHA ===

        public function enviarRecuperacaoSenha($email) {
            $usuario = $this->dao->buscarPorEmail($email);
            if (!$usuario) {
                return false;
            }

            $usuarioId = $usuario->getId();
            $this->verificacaoDao->limparPorUsuario($usuarioId, 'recuperacao');

            $token = bin2hex(random_bytes(32));
            $codigo = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $expiracao = date('Y-m-d H:i:s', strtotime('+30 minutes'));

            $this->verificacaoDao->salvar($usuarioId, $token, $codigo, $expiracao, 'recuperacao');

            $emailService = new EmailService();
            return $emailService->enviarRecuperacao(
                $usuario->getEmail(),
                $usuario->getNome(),
                $token,
                $codigo
            );
        }

        public function redefinirSenha($usuarioId, $novaSenha) {
            $validacao = validarSenhaForte($novaSenha);
            if ($validacao !== true) {
                return ["success" => false, "message" => $validacao];
            }
            $this->dao->updateSenha($usuarioId, $novaSenha);
            return ["success" => true, "message" => "Senha redefinida com sucesso!"];
        }

        // === ALTERAÇÃO DE EMAIL ===

        public function iniciarAlteracaoEmail($usuarioId, $novoEmail) {
            $usuarioAtual = $this->dao->readPorId($usuarioId);
            if (!$usuarioAtual) {
                return ["success" => false, "message" => "Usuário não encontrado."];
            }

            if (!filter_var($novoEmail, FILTER_VALIDATE_EMAIL)) {
                return ["success" => false, "message" => "Email inválido."];
            }

            if ($novoEmail === $usuarioAtual->getEmail()) {
                return ["success" => false, "message" => "O novo email é igual ao atual."];
            }

            $token = bin2hex(random_bytes(32));
            $this->dao->salvarEmailPendente($usuarioId, $novoEmail, $token);

            $emailService = new EmailService();
            $enviou = $emailService->enviarAlteracaoEmail($novoEmail, $usuarioAtual->getNome(), $token);

            if ($enviou) {
                return ["success" => true, "message" => "Enviamos um link de confirmação para o novo email.", "token" => $token];
            }

            return ["success" => false, "message" => "Erro ao enviar email. Tente novamente."];
        }

        public function confirmarAlteracaoEmail($token) {
            $usuario = $this->dao->confirmarEmailPendente($token);
            if (!$usuario) {
                return ["success" => false, "message" => "Link inválido ou expirado."];
            }
            return ["success" => true, "message" => "Email alterado com sucesso!", "usuario" => $usuario];
        }
    }