<?php

require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/mensagem.php";

class MensagemDAO {
    private $pdo;

    public function __construct() {
        $this->pdo = Conexao::getConexao();
    }

    public function enviar(Mensagem $mensagem) {
        $sql = "INSERT INTO tb_mensagens (conversa_id, remetente_id, conteudo) VALUES (?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $mensagem->getConversaId(),
            $mensagem->getRemetenteId(),
            $mensagem->getConteudo()
        ]);
        $mensagem->setId($this->pdo->lastInsertId());

        $sqlData = "SELECT data_envio FROM tb_mensagens WHERE id = ?";
        $stmtData = $this->pdo->prepare($sqlData);
        $stmtData->execute([$mensagem->getId()]);
        $row = $stmtData->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $mensagem->setDataEnvio($row['data_envio']);
        }

        return $mensagem;
    }

    public function listarPorConversa($conversaId, $antes = null, $limite = 50) {
        $limite = max(1, (int) $limite);
        if ($antes) {
            $sql = "SELECT m.*, u.nome AS remetente_nome, u.foto_perfil AS remetente_foto
                    FROM tb_mensagens m
                    INNER JOIN tb_usuarios u ON m.remetente_id = u.id
                    WHERE m.conversa_id = ? AND m.id < ?
                    ORDER BY m.data_envio DESC
                    LIMIT $limite";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$conversaId, $antes]);
        } else {
            $sql = "SELECT m.*, u.nome AS remetente_nome, u.foto_perfil AS remetente_foto
                    FROM tb_mensagens m
                    INNER JOIN tb_usuarios u ON m.remetente_id = u.id
                    WHERE m.conversa_id = ?
                    ORDER BY m.data_envio DESC
                    LIMIT $limite";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$conversaId]);
        }
        return array_reverse($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function novasDesde($conversaId, $ultimoId) {
        $sql = "SELECT m.*, u.nome AS remetente_nome, u.foto_perfil AS remetente_foto
                FROM tb_mensagens m
                INNER JOIN tb_usuarios u ON m.remetente_id = u.id
                WHERE m.conversa_id = ? AND m.id > ?
                ORDER BY m.data_envio ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$conversaId, $ultimoId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
