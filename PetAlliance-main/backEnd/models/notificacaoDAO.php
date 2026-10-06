<?php

require_once __DIR__ . "/../../backEnd/config/config.php";
require_once __DIR__ . "/../../backEnd/models/notificacao.php";

class NotificacaoDAO {
    private $pdo;

    public function __construct() {
        $this->pdo = Conexao::getConexao();
    }

    public function criar(Notificacao $notificacao) {
        $sql = "INSERT INTO tb_notificacoes (usuario_id, tipo, mensagem, link) VALUES (?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $notificacao->getUsuarioId(),
            $notificacao->getTipo(),
            $notificacao->getMensagem(),
            $notificacao->getLink()
        ]);
        return $this->pdo->lastInsertId();
    }

    public function listarPorUsuario($usuarioId) {
        $sql = "SELECT * FROM tb_notificacoes WHERE usuario_id = ? ORDER BY criado_em DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$usuarioId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function marcarLida($id) {
        $sql = "UPDATE tb_notificacoes SET lida = 1 WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function naoLidas($usuarioId) {
        $sql = "SELECT COUNT(*) FROM tb_notificacoes WHERE usuario_id = ? AND lida = 0";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$usuarioId]);
        return (int) $stmt->fetchColumn();
    }
}
