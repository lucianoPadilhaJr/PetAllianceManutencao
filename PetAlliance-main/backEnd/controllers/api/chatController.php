<?php

require_once __DIR__ . "/../../config/config.php";
require_once __DIR__ . "/../../models/mensagem.php";
require_once __DIR__ . "/../../models/mensagemDAO.php";
require_once __DIR__ . "/../../models/conversaDAO.php";

class ChatController {
    private $mensagemDAO;
    private $conversaDAO;

    public function __construct() {
        $this->mensagemDAO = new MensagemDAO();
        $this->conversaDAO = new ConversaDAO();
    }

    public function enviarMensagem() {
        header('Content-Type: application/json');
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $solicitacaoId = $input['solicitacao_id'] ?? null;
            $remetenteId = $input['remetente_id'] ?? null;
            $conteudo = trim($input['conteudo'] ?? '');

            if (!$solicitacaoId || !$remetenteId || $conteudo === '') {
                echo json_encode(['sucesso' => false, 'erro' => 'Dados incompletos']);
                return;
            }

            $conversa = $this->conversaDAO->buscarPorSolicitacao($solicitacaoId);
            if (!$conversa) {
                $conversaId = $this->conversaDAO->criar($solicitacaoId);
            } else {
                $conversaId = $conversa['id'];
            }

            $mensagem = new Mensagem($conversaId, $remetenteId, $conteudo);
            $mensagem = $this->mensagemDAO->enviar($mensagem);

            echo json_encode(['sucesso' => true, 'mensagem' => $mensagem->jsonSerialize()]);
        } catch (Exception $e) {
            echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
        }
    }

    public function listarMensagens() {
        header('Content-Type: application/json');
        try {
            $conversaId = $_GET['conversa_id'] ?? null;
            $antes = $_GET['antes'] ?? null;

            if (!$conversaId) {
                echo json_encode(['sucesso' => false, 'erro' => 'Conversa não informada']);
                return;
            }

            $mensagens = $this->mensagemDAO->listarPorConversa($conversaId, $antes);
            echo json_encode(['sucesso' => true, 'mensagens' => $mensagens]);
        } catch (Exception $e) {
            echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
        }
    }

    public function novasMensagens() {
        header('Content-Type: application/json');
        try {
            $conversaId = $_GET['conversa_id'] ?? null;
            $ultimoId = (int) ($_GET['ultimo_id'] ?? 0);

            if (!$conversaId) {
                echo json_encode(['sucesso' => false, 'erro' => 'Conversa não informada']);
                return;
            }

            $mensagens = $this->mensagemDAO->novasDesde($conversaId, $ultimoId);
            echo json_encode(['sucesso' => true, 'mensagens' => $mensagens]);
        } catch (Exception $e) {
            echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
        }
    }

    public function listarConversas() {
        header('Content-Type: application/json');
        try {
            $usuarioId = $_GET['usuario_id'] ?? null;
            if (!$usuarioId) {
                echo json_encode(['sucesso' => false, 'erro' => 'Usuário não informado']);
                return;
            }
            $conversas = $this->conversaDAO->listarPorUsuario($usuarioId);
            echo json_encode(['sucesso' => true, 'conversas' => $conversas]);
        } catch (Exception $e) {
            echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
        }
    }

    public function dadosConversa() {
        header('Content-Type: application/json');
        try {
            $solicitacaoId = $_GET['solicitacao_id'] ?? null;
            if (!$solicitacaoId) {
                echo json_encode(['sucesso' => false, 'erro' => 'Solicitação não informada']);
                return;
            }

            $conversa = $this->conversaDAO->buscarPorSolicitacao($solicitacaoId);
            $conversaId = $conversa ? $conversa['id'] : null;

            $pdo = Conexao::getConexao();
            $sql = "SELECT s.*, p.nome AS pet_nome, p.foto_pet, p.dono_id,
                           COALESCE(u.nome, 'Usuário') AS dono_nome, COALESCE(u.foto_perfil, 'placeholder.webp') AS dono_foto,
                           COALESCE(rem.nome, 'Usuário') AS remetente_nome, COALESCE(rem.foto_perfil, 'placeholder.webp') AS remetente_foto
                    FROM tb_solicitacoes_match s
                    INNER JOIN tb_pets p ON s.pet_id = p.id
                    LEFT JOIN tb_usuarios u ON p.dono_id = u.id
                    LEFT JOIN tb_usuarios rem ON s.remetente_id = rem.id
                    WHERE s.id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$solicitacaoId]);
            $dados = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$dados) {
                echo json_encode(['sucesso' => false, 'erro' => 'Solicitação não encontrada']);
                return;
            }

            $dados['conversa_id'] = $conversaId;
            echo json_encode(['sucesso' => true, 'dados' => $dados]);
        } catch (Exception $e) {
            echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
        }
    }

}
