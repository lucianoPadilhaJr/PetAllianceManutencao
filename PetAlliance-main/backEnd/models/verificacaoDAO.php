<?php

require_once __DIR__ . "/../../backEnd/config/config.php";

class VerificacaoDAO {
    private $pdo;

    public function __construct() {
        $this->pdo = Conexao::getConexao();
    }

    public function salvar($usuarioId, $token, $codigo, $expiracao, $tipo = 'verificacao') {
        $sql = "INSERT INTO tb_verificacao_email (usuario_id, token, codigo, tipo, expiracao) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$usuarioId, $token, $codigo, $tipo, $expiracao]);
    }

    public function buscarPorToken($token, $tipo = null) {
        $sql = "SELECT * FROM tb_verificacao_email WHERE token = ? AND usado = FALSE AND expiracao > NOW()";
        $params = [$token];
        if ($tipo) {
            $sql .= " AND tipo = ?";
            $params[] = $tipo;
        }
        $sql .= " ORDER BY criado_em DESC LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function buscarPorUsuario($usuarioId, $tipo = null) {
        $sql = "SELECT * FROM tb_verificacao_email WHERE usuario_id = ? AND usado = FALSE AND expiracao > NOW()";
        $params = [$usuarioId];
        if ($tipo) {
            $sql .= " AND tipo = ?";
            $params[] = $tipo;
        }
        $sql .= " ORDER BY criado_em DESC LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function buscarUltimoPorUsuario($usuarioId, $tipo = null) {
        $sql = "SELECT * FROM tb_verificacao_email WHERE usuario_id = ?";
        $params = [$usuarioId];
        if ($tipo) {
            $sql .= " AND tipo = ?";
            $params[] = $tipo;
        }
        $sql .= " ORDER BY criado_em DESC LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function marcarUsado($id) {
        $sql = "UPDATE tb_verificacao_email SET usado = TRUE WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function limparPorUsuario($usuarioId, $tipo = null) {
        $sql = "DELETE FROM tb_verificacao_email WHERE usuario_id = ?";
        $params = [$usuarioId];
        if ($tipo) {
            $sql .= " AND tipo = ?";
            $params[] = $tipo;
        }
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }
}
