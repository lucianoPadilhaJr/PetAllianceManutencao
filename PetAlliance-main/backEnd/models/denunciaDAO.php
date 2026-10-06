<?php

require_once __DIR__ . "/../../backEnd/config/config.php";
require_once __DIR__ . "/../../backEnd/models/denuncia.php";

class DenunciaDAO {

    private $pdo;

    public function __construct() {
        $this->pdo = Conexao::getConexao();
    }

    public function cadastrar(Denuncia $denuncia) {

        $sql = "INSERT INTO tb_denuncias
                (usuario_id, tipo_alvo, alvo_id, descricao, resolvido)
                VALUES (?, ?, ?, ?, ?)";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            $denuncia->getUsuarioId(),
            $denuncia->getTipoAlvo(),
            $denuncia->getAlvoId(),
            $denuncia->getDescricao(),
            $denuncia->getResolvido()
        ]);

        return $denuncia;
    }

    public function listar() {
        $sql = "SELECT d.*, u.nome AS usuario_nome, u.foto_perfil AS usuario_imagem
                FROM tb_denuncias d
                LEFT JOIN tb_usuarios u ON d.usuario_id = u.id
                ORDER BY d.resolvido ASC, d.id DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function atualizarStatus($id, $status) {
        $sql = "UPDATE tb_denuncias SET resolvido = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$status, $id]);
    }

    public function estatisticas() {
        $total = $this->pdo->query("SELECT COUNT(*) FROM tb_denuncias")->fetchColumn();

        $porTipo = $this->pdo->query(
            "SELECT tipo_alvo, COUNT(*) as total FROM tb_denuncias GROUP BY tipo_alvo"
        )->fetchAll(PDO::FETCH_ASSOC);

        $resolvidas = $this->pdo->query(
            "SELECT resolvido, COUNT(*) as total FROM tb_denuncias GROUP BY resolvido"
        )->fetchAll(PDO::FETCH_ASSOC);

        return [
            'total' => (int) $total,
            'porTipo' => $porTipo,
            'resolvidas' => $resolvidas
        ];
    }
}