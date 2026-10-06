<?php

require_once __DIR__ . "/../../config/config.php";
require_once __DIR__ . "/../../models/notificacao.php";
require_once __DIR__ . "/../../models/notificacaoDAO.php";
require_once __DIR__ . "/../../models/solicitacaoMatchDAO.php";

class NotificacaoController {
    private $dao;
    private $solicitacaoDAO;

    public function __construct() {
        $this->dao = new NotificacaoDAO();
        $this->solicitacaoDAO = new SolicitacaoMatchDAO();
    }

    public function listar($usuarioId) {
        header('Content-Type: application/json');
        try {
            $todas = $this->dao->listarPorUsuario($usuarioId);
            $naoLidas = $this->dao->naoLidas($usuarioId);

            $matchAceitos = [];
            $matchRecusados = [];
            $matchRecebidas = [];
            $outras = [];

            foreach ($todas as $n) {
                switch ($n['tipo']) {
                    case 'match_aceito':
                        $matchAceitos[] = $n;
                        break;
                    case 'match_recusado':
                        $matchRecusados[] = $n;
                        break;
                    case 'solicitacao_match':
                        $matchRecebidas[] = $n;
                        break;
                    default:
                        $outras[] = $n;
                        break;
                }
            }

            if (empty($matchRecebidas) && $this->solicitacaoDAO) {
                $solicitacoesPendentes = $this->solicitacaoDAO->listarPorDono($usuarioId);
                foreach ($solicitacoesPendentes as $solicitacao) {
                    if (($solicitacao['status'] ?? '') !== 'pendente') {
                        continue;
                    }

                    $matchRecebidas[] = [
                        'id' => 'solicitacao_' . $solicitacao['id'],
                        'usuario_id' => $usuarioId,
                        'tipo' => 'solicitacao_match',
                        'mensagem' => 'Alguém quer um match com seu pet "' . ($solicitacao['pet_nome'] ?? 'pet') . '"',
                        'link' => '/backEnd/match.php',
                        'lida' => 0,
                        'criado_em' => $solicitacao['criado_em'] ?? null
                    ];
                }
            }

            echo json_encode([
                'sucesso' => true,
                'naoLidas' => $naoLidas,
                'matchAceitos' => $matchAceitos,
                'matchRecusados' => $matchRecusados,
                'matchRecebidas' => $matchRecebidas,
                'outras' => $outras
            ]);
        } catch (Exception $e) {
            echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
        }
    }

    public function marcarLida() {
        header('Content-Type: application/json');
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $id = $input['id'] ?? null;
            if (!$id) {
                echo json_encode(['sucesso' => false, 'erro' => 'ID não informado']);
                return;
            }
            $this->dao->marcarLida($id);
            echo json_encode(['sucesso' => true]);
        } catch (Exception $e) {
            echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
        }
    }
}
