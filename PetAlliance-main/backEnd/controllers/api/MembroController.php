<?php
    require_once __DIR__ . "/../../config/config.php";
    require_once __DIR__ . "/../../config/planos.php";

    class MembroController {
        private $pdo;

        public function __construct() {
            $this->pdo = Conexao::getConexao();
        }

        public function getPlanoAtivo($usuarioId) {
            $sql = "SELECT * FROM tb_pagamentos 
                    WHERE usuario_id = ? AND status_pagamento = 'PAID' 
                    AND (plano_expiracao IS NULL OR plano_expiracao >= CURDATE())
                    ORDER BY criado_em DESC LIMIT 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$usuarioId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        public function listarDestaques() {
            $sql = "SELECT a.*, 
                           (SELECT pf.foto_path FROM tb_pets_fotos pf WHERE pf.pet_id = a.id ORDER BY pf.ordem ASC LIMIT 1) as foto_pet,
                           u.nome as dono_nome, u.foto_perfil as dono_foto
                    FROM tb_pets_membros pm
                    JOIN tb_pets a ON a.id = pm.animal_id
                    JOIN tb_usuarios u ON u.id = pm.usuario_id
                    ORDER BY pm.criado_em DESC
                    LIMIT 50";
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function listarMeusDestaques($usuarioId) {
            $sql = "SELECT a.* FROM tb_pets_membros pm
                    JOIN tb_pets a ON a.id = pm.animal_id
                    WHERE pm.usuario_id = ?
                    ORDER BY pm.criado_em DESC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$usuarioId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function destacarAnimal($usuarioId, $animalId) {
            $plano = $this->getPlanoAtivo($usuarioId);
            if (!$plano) {
                echo json_encode(['success' => false, 'error' => 'Você não possui um plano ativo.']);
                return;
            }

            $maxDestaques = (int)($plano['plano_max_destaques'] ?? 0);

            $stmtCount = $this->pdo->prepare("SELECT COUNT(*) FROM tb_pets_membros WHERE usuario_id = ?");
            $stmtCount->execute([$usuarioId]);
            $qtdAtual = (int)$stmtCount->fetchColumn();

            if ($qtdAtual >= $maxDestaques) {
                echo json_encode(['success' => false, 'error' => "Limite de {$maxDestaques} animais destacados atingido."]);
                return;
            }

            $stmtCheck = $this->pdo->prepare("SELECT COUNT(*) FROM tb_pets_membros WHERE usuario_id = ? AND animal_id = ?");
            $stmtCheck->execute([$usuarioId, $animalId]);
            if ($stmtCheck->fetchColumn() > 0) {
                echo json_encode(['success' => false, 'error' => 'Este animal já está destacado.']);
                return;
            }

            $stmtInsert = $this->pdo->prepare("INSERT INTO tb_pets_membros (usuario_id, animal_id) VALUES (?, ?)");
            $stmtInsert->execute([$usuarioId, $animalId]);

            echo json_encode(['success' => true, 'message' => 'Animal destacado com sucesso!']);
        }

        public function removerDestaque($usuarioId, $animalId) {
            $stmt = $this->pdo->prepare("DELETE FROM tb_pets_membros WHERE usuario_id = ? AND animal_id = ?");
            $stmt->execute([$usuarioId, $animalId]);

            if ($stmt->rowCount() > 0) {
                echo json_encode(['success' => true, 'message' => 'Destaque removido.']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Animal não encontrado nos destaques.']);
            }
        }

        public function jsonAnimaisDestaque() {
            $animais = $this->listarDestaques();
            echo json_encode($animais);
        }
    }
