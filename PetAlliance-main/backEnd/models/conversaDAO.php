<?php

require_once __DIR__ . "/../config/config.php";

class ConversaDAO {
    private $pdo;

    public function __construct() {
        $this->pdo = Conexao::getConexao();
    }

    public function criar($solicitacaoId) {
        $sql = "INSERT INTO tb_conversas (solicitacao_id) VALUES (?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$solicitacaoId]);
        return $this->pdo->lastInsertId();
    }

    public function buscarPorSolicitacao($solicitacaoId) {
        $sql = "SELECT * FROM tb_conversas WHERE solicitacao_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$solicitacaoId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function buscar($id) {
        $sql = "SELECT * FROM tb_conversas WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function listarPorUsuario($usuarioId) {
        $sql = "SELECT c.id, c.ativa, s.id AS solicitacao_id, s.pet_id, p.nome AS pet_nome, p.foto_pet,
                       s.remetente_id,
                       CASE WHEN s.remetente_id = ? THEN COALESCE(u.nome, 'Usuário') ELSE COALESCE(u2.nome, 'Usuário') END AS outro_nome,
                       CASE WHEN s.remetente_id = ? THEN COALESCE(u.foto_perfil, 'placeholder.webp') ELSE COALESCE(u2.foto_perfil, 'placeholder.webp') END AS outro_foto
                FROM tb_solicitacoes_match s
                INNER JOIN tb_pets p ON s.pet_id = p.id
                LEFT JOIN tb_usuarios u ON p.dono_id = u.id
                LEFT JOIN tb_usuarios u2 ON s.remetente_id = u2.id
                LEFT JOIN tb_conversas c ON c.solicitacao_id = s.id
                WHERE (p.dono_id = ? OR s.remetente_id = ?) AND s.status = 'aceito'
                ORDER BY c.id DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$usuarioId, $usuarioId, $usuarioId, $usuarioId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
