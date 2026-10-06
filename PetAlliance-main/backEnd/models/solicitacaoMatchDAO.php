<?php

require_once __DIR__ . "/../../backEnd/config/config.php";
require_once __DIR__ . "/../../backEnd/models/solicitacaoMatch.php";

class SolicitacaoMatchDAO {
    private $pdo;

    public function __construct() {
        $this->pdo = Conexao::getConexao();
    }

    public function criar(SolicitacaoMatch $solicitacao) {
        $sql = "INSERT INTO tb_solicitacoes_match (pet_id, remetente_id, status) VALUES (?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $solicitacao->getPetId(),
            $solicitacao->getRemetenteId(),
            $solicitacao->getStatus()
        ]);
        $solicitacao->setId($this->pdo->lastInsertId());
        return $solicitacao;
    }

    public function listarPorDono($donoId) {
        $sql = "SELECT s.*, p.nome AS pet_nome, p.foto_pet AS pet_foto,
                       COALESCE(u.nome, 'Usuário') AS remetente_nome, COALESCE(u.foto_perfil, 'placeholder.webp') AS remetente_imagem
                FROM tb_solicitacoes_match s
                INNER JOIN tb_pets p ON s.pet_id = p.id
                LEFT JOIN tb_usuarios u ON s.remetente_id = u.id
                WHERE p.dono_id = ?
                ORDER BY FIELD(s.status, 'pendente', 'aceito', 'recusado'), s.criado_em DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$donoId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarPorRemetente($remetenteId) {
        $sql = "SELECT s.*, p.nome AS pet_nome, p.foto_pet AS pet_foto
                FROM tb_solicitacoes_match s
                INNER JOIN tb_pets p ON s.pet_id = p.id
                WHERE s.remetente_id = ?
                ORDER BY FIELD(s.status, 'pendente', 'aceito', 'recusado'), s.criado_em DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$remetenteId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function atualizarStatus($id, $status) {
        $sql = "UPDATE tb_solicitacoes_match SET status = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$status, $id]);
    }

    public function buscar($id) {
        $sql = "SELECT s.*, p.dono_id AS dono_id, p.nome AS pet_nome
                FROM tb_solicitacoes_match s
                INNER JOIN tb_pets p ON s.pet_id = p.id
                WHERE s.id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function verificarExistente($petId, $remetenteId) {
        $sql = "SELECT 1 FROM tb_solicitacoes_match WHERE pet_id = ? AND remetente_id = ? AND status = 'pendente'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$petId, $remetenteId]);
        return (bool) $stmt->fetch();
    }
}
