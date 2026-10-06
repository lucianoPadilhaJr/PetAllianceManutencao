<?php

require_once __DIR__ . "/../../config/config.php";
require_once __DIR__ . "/../../models/solicitacaoMatch.php";
require_once __DIR__ . "/../../models/solicitacaoMatchDAO.php";
require_once __DIR__ . "/../../models/notificacao.php";
require_once __DIR__ . "/../../models/notificacaoDAO.php";

class SolicitacaoMatchController {
    private $dao;
    private $notificacaoDAO;

    public function __construct() {
        $this->dao = new SolicitacaoMatchDAO();
        $this->notificacaoDAO = new NotificacaoDAO();
    }

    public function enviarSolicitacao() {
        header('Content-Type: application/json');
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $petId = $input['pet_id'] ?? null;
            $remetenteId = $input['remetente_id'] ?? null;

            if (!$petId || !$remetenteId) {
                echo json_encode(['sucesso' => false, 'erro' => 'Dados incompletos']);
                return;
            }

            if ($this->dao->verificarExistente($petId, $remetenteId)) {
                echo json_encode(['sucesso' => false, 'erro' => 'Você já enviou uma solicitação para este pet']);
                return;
            }

            $solicitacao = new SolicitacaoMatch($petId, $remetenteId);
            $solicitacao = $this->dao->criar($solicitacao);

            $solicitacaoData = $this->dao->buscar($solicitacao->getId());
            $donoId = $solicitacaoData['dono_id'];

            try {
                $notificacao = new Notificacao(
                    $donoId,
                    'solicitacao_match',
                    'Alguém quer um match com seu pet "' . $solicitacaoData['pet_nome'] . '"',
                    '/backEnd/match.php'
                );
                $this->notificacaoDAO->criar($notificacao);
            } catch (Exception $e) {
                // Ignora: dono do pet pode ter sido deletado
            }

            echo json_encode(['sucesso' => true, 'mensagem' => 'Solicitação enviada!', 'solicitacao_id' => (int) $solicitacao->getId()]);
        } catch (Exception $e) {
            echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
        }
    }

    public function aceitarSolicitacao() {
        header('Content-Type: application/json');
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $id = $input['id'] ?? null;

            if (!$id) {
                echo json_encode(['sucesso' => false, 'erro' => 'ID não informado']);
                return;
            }

            $solicitacao = $this->dao->buscar($id);
            if (!$solicitacao) {
                echo json_encode(['sucesso' => false, 'erro' => 'Solicitação não encontrada']);
                return;
            }

            $this->dao->atualizarStatus($id, 'aceito');

            try {
                $notificacao = new Notificacao(
                    $solicitacao['remetente_id'],
                    'match_aceito',
                    'Seu match com "' . $solicitacao['pet_nome'] . '" foi aceito!',
                    '/backEnd/match.php'
                );
                $this->notificacaoDAO->criar($notificacao);
            } catch (Exception $e) {
                // Ignora: remetente pode ter sido deletado
            }

            echo json_encode(['sucesso' => true, 'mensagem' => 'Match aceito!']);
        } catch (Exception $e) {
            echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
        }
    }

    public function recusarSolicitacao() {
        header('Content-Type: application/json');
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $id = $input['id'] ?? null;

            if (!$id) {
                echo json_encode(['sucesso' => false, 'erro' => 'ID não informado']);
                return;
            }

            $solicitacao = $this->dao->buscar($id);
            if (!$solicitacao) {
                echo json_encode(['sucesso' => false, 'erro' => 'Solicitação não encontrada']);
                return;
            }

            $this->dao->atualizarStatus($id, 'recusado');

            try {
                $notificacao = new Notificacao(
                    $solicitacao['remetente_id'],
                    'match_recusado',
                    'Seu match com "' . $solicitacao['pet_nome'] . '" foi recusado.',
                    '/backEnd/match.php'
                );
                $this->notificacaoDAO->criar($notificacao);
            } catch (Exception $e) {
                // Ignora: remetente pode ter sido deletado
            }

            echo json_encode(['sucesso' => true, 'mensagem' => 'Solicitação recusada.']);
        } catch (Exception $e) {
            echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
        }
    }

    public function listarSolicitacoes($donoId) {
        header('Content-Type: application/json');
        try {
            $solicitacoes = $this->dao->listarPorDono($donoId);
            echo json_encode(['sucesso' => true, 'solicitacoes' => $solicitacoes]);
        } catch (Exception $e) {
            echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
        }
    }

    public function listarMinhasSolicitacoes($remetenteId) {
        header('Content-Type: application/json');
        try {
            $solicitacoes = $this->dao->listarPorRemetente($remetenteId);
            echo json_encode(['sucesso' => true, 'solicitacoes' => $solicitacoes]);
        } catch (Exception $e) {
            echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
        }
    }
}
