<?php

require_once __DIR__ . "/../../config/config.php";
require_once __DIR__ . "/../../models/denuncia.php";
require_once __DIR__ . "/../../models/denunciaDAO.php";

class DenunciaController {

    private $dao;

    public function __construct() {
        $this->dao = new DenunciaDAO();
    }

    public function criarDenuncia(Denuncia $denuncia) {
        $pdo = Conexao::getConexao();
        $alvoId = $denuncia->getAlvoId();

        if ($denuncia->getTipoAlvo() === 'animal' && $alvoId) {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM tb_pets WHERE id = ?");
            $stmt->execute([$alvoId]);
            if (!$stmt->fetchColumn()) {
                throw new InvalidArgumentException("Animal alvo da denúncia não encontrado.");
            }
        }

        if ($denuncia->getTipoAlvo() === 'usuario' && $alvoId) {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM tb_usuarios WHERE id = ?");
            $stmt->execute([$alvoId]);
            if (!$stmt->fetchColumn()) {
                throw new InvalidArgumentException("Usuário alvo da denúncia não encontrado.");
            }
        }

        try {
            return $this->dao->cadastrar($denuncia);
        } catch (InvalidArgumentException $e) {
            throw $e;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function listarDenuncias() {
        header('Content-Type: application/json');
        try {
            $denuncias = $this->dao->listar();
            echo json_encode(['sucesso' => true, 'denuncias' => $denuncias]);
        } catch (Exception $e) {
            echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
        }
    }

    public function resolverDenuncia() {
        header('Content-Type: application/json');
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $id = $input['id'] ?? null;
            $resolvido = isset($input['resolvido']) ? (int) $input['resolvido'] : null;
            if (!$id || $resolvido === null) {
                echo json_encode(['sucesso' => false, 'erro' => 'ID ou status não informado']);
                return;
            }
            $this->dao->atualizarStatus($id, $resolvido);
            echo json_encode(['sucesso' => true]);
        } catch (Exception $e) {
            echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
        }
    }

    public function estatisticas() {
        header('Content-Type: application/json');
        try {
            $stats = $this->dao->estatisticas();
            echo json_encode(['sucesso' => true, 'estatisticas' => $stats]);
        } catch (Exception $e) {
            echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
        }
    }
}