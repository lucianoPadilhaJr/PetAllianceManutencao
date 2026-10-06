<?php

    require_once __DIR__ . "/../../backEnd/config/config.php";
    require_once __DIR__ . "/../../backEnd/models/animal.php";

    class AnimalDAO {
        private $pdo;

        public function __construct() {
            $this->pdo = Conexao::getConexao();
        }

        public function carregarFotos($petId) {
            $sql = "SELECT foto_path FROM tb_pets_fotos WHERE pet_id = ? ORDER BY ordem ASC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$petId]);
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        }

        public function cadastrarAnimal(Animal $animal) {
            try {
                $sql = "INSERT INTO tb_pets (dono_id, nome, raca, cor, sexo, tipo, porte, data_nascimento, peso, descricao, vacinado, certificado_raca, foto_certificado, foto_vacinas) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([
                    $animal->getDonoid(),
                    $animal->getNome(),
                    $animal->getRaca(),
                    $animal->getCor(),
                    $animal->getSexo(),
                    $animal->getTipo(),
                    $animal->getPorte(),
                    $animal->getDataNascimento(),
                    $animal->getPeso(),
                    $animal->getDescricao(),
                    $animal->getVacinado(),
                    $animal->getCertificado(),
                    $animal->getFotoCertificado(),
                    $animal->getFotoVacinas()
                ]);
                $petId = $this->pdo->lastInsertId();
                $animal->setId($petId);

                $fotos = $animal->getFotos();
                if (!empty($fotos)) {
                    $stmtFoto = $this->pdo->prepare("INSERT INTO tb_pets_fotos (pet_id, foto_path, ordem) VALUES (?, ?, ?)");
                    foreach ($fotos as $i => $path) {
                        $stmtFoto->execute([$petId, $path, $i]);
                    }
                }

                return $animal;
            } catch (PDOException $e) {
                if($e->errorInfo[1] == 1062) {
                    throw new InvalidArgumentException("invalido");
                }
                throw $e;
            }
        }

        public function favoritarAnimal($usuarioId, $petId) {
            $sql = "SELECT 1 FROM tb_favoritos WHERE id_usuario = ? AND id_pet = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$usuarioId, $petId]);
            $favorito = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($favorito) {
                $sqlDelete = "DELETE FROM tb_favoritos WHERE id_usuario = ? AND id_pet = ?";
                $stmtDelete = $this->pdo->prepare($sqlDelete);
                $stmtDelete->execute([$usuarioId, $petId]);
                return false;
            }

            $sqlInsert = "INSERT INTO tb_favoritos (id_pet, id_usuario) VALUES (?, ?)";
            $stmtInsert = $this->pdo->prepare($sqlInsert);
            $stmtInsert->execute([$petId, $usuarioId]);
            return true;
        }

        public function listarFavoritos($id) {
            try {
                $sql = "SELECT p.*, 1 AS favoritado
                    FROM tb_pets p
                    INNER JOIN tb_favoritos pf
                    ON p.id = pf.id_pet
                    WHERE pf.id_usuario = ?
                ";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([$id]);
                $animais = [];

                while ($dados = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $fotos = $this->carregarFotos($dados['id']);
                    $animal = new Animal(
                        $dados['dono_id'],
                        $dados['nome'],
                        $dados['raca'],
                        $dados['cor'],
                        $dados['sexo'],
                        $dados['tipo'],
                        $dados['porte'],
                        $dados['data_nascimento'],
                        $dados['peso'],
                        $dados['descricao'],
                        $dados['vacinado'],
                        $dados['certificado_raca'],
                        $dados['foto_certificado'],
                        $dados['foto_vacinas'],
                        $dados['id'] ?? null,
                        $dados['favoritado'] ?? 1,
                        $fotos
                    );
                    $animais[] = $animal;
                }
                return $animais;
            } catch (PDOException $e) {
                throw $e;
            }
        }

        public function read($id, $usuarioId = null) {
            $sql = "SELECT * FROM tb_pets WHERE id = ?";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            $dados = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$dados) {
                return null;
            }

            $favoritado = false;
            if ($usuarioId) {
                $stmtFav = $this->pdo->prepare("SELECT 1 FROM tb_favoritos WHERE id_usuario = ? AND id_pet = ?");
                $stmtFav->execute([$usuarioId, $id]);
                $favoritado = (bool)$stmtFav->fetchColumn();
            }

            $fotos = $this->carregarFotos($dados['id']);
            $animal = new Animal(
                $dados['dono_id'],
                $dados['nome'],
                $dados['raca'],
                $dados['cor'],
                $dados['sexo'],
                $dados['tipo'],
                $dados['porte'],
                $dados['data_nascimento'],
                $dados['peso'],
                $dados['descricao'],
                $dados['vacinado'],
                $dados['certificado_raca'],
                $dados['foto_certificado'],
                $dados['foto_vacinas'],
                $dados['id'] ?? null,
                $favoritado,
                $fotos
            );

            return $animal;
        }

        public function search($termo, $usuarioId, $filtros = [], $pagina = 1) {
            $limite = 24;
            $offset = ($pagina - 1) * $limite;
            $condicoes = [];
            $params = [$usuarioId];

            if ($termo !== '') {
                $termoLike = '%' . $termo . '%';
                $condicoes[] = "(p.nome LIKE ? OR p.tipo LIKE ? OR p.raca LIKE ? OR p.cor LIKE ? OR p.descricao LIKE ?)";
                $params = array_merge($params, [$termoLike, $termoLike, $termoLike, $termoLike, $termoLike]);
            }

            if (!empty($filtros['porte'])) {
                $portes = explode(',', $filtros['porte']);
                $placeholders = implode(',', array_fill(0, count($portes), '?'));
                $condicoes[] = "p.porte IN ($placeholders)";
                $params = array_merge($params, $portes);
            }

            if (!empty($filtros['cor'])) {
                $cores = explode(',', $filtros['cor']);
                $coresPadrao = ['Preto', 'Branco', 'Marrom', 'Cinza'];
                $selecionadas = array_diff($cores, ['Outro']);
                $temOutro = in_array('Outro', $cores);

                if ($temOutro) {
                    $excluir = array_diff($coresPadrao, $selecionadas);
                    if (!empty($excluir)) {
                        $placeholders = implode(',', array_fill(0, count($excluir), '?'));
                        $condicoes[] = "p.cor NOT IN ($placeholders)";
                        $params = array_merge($params, array_values($excluir));
                    }
                } elseif (!empty($selecionadas)) {
                    $placeholders = implode(',', array_fill(0, count($selecionadas), '?'));
                    $condicoes[] = "p.cor IN ($placeholders)";
                    $params = array_merge($params, array_values($selecionadas));
                }
            }

            if (!empty($filtros['tipo'])) {
                $tipos = explode(',', $filtros['tipo']);
                $tiposPadrao = ['Cachorro', 'Gato', 'Cavalo'];
                $selecionadas = array_diff($tipos, ['Outro']);
                $temOutro = in_array('Outro', $tipos);

                if ($temOutro) {
                    $excluir = array_diff($tiposPadrao, $selecionadas);
                    if (!empty($excluir)) {
                        $placeholders = implode(',', array_fill(0, count($excluir), '?'));
                        $condicoes[] = "p.tipo NOT IN ($placeholders)";
                        $params = array_merge($params, array_values($excluir));
                    }
                } elseif (!empty($selecionadas)) {
                    $placeholders = implode(',', array_fill(0, count($selecionadas), '?'));
                    $condicoes[] = "p.tipo IN ($placeholders)";
                    $params = array_merge($params, array_values($selecionadas));
                }
            }

            if (!empty($filtros['vacinado'])) {
                $vacinados = explode(',', $filtros['vacinado']);
                $placeholders = implode(',', array_fill(0, count($vacinados), '?'));
                $condicoes[] = "p.vacinado IN ($placeholders)";
                $params = array_merge($params, $vacinados);
            }

            if (!empty($filtros['certificado'])) {
                $certificados = explode(',', $filtros['certificado']);
                $placeholders = implode(',', array_fill(0, count($certificados), '?'));
                $condicoes[] = "p.certificado_raca IN ($placeholders)";
                $params = array_merge($params, $certificados);
            }

            $condicoes[] = 'p.dono_id != ?';
            $params[] = $usuarioId;

            $where = count($condicoes) > 0 ? 'WHERE ' . implode(' AND ', $condicoes) : '';
            $whereParams = array_slice($params, 1);

            $countSql = "SELECT COUNT(*) FROM tb_pets p $where";
            $countStmt = $this->pdo->prepare($countSql);
            $countStmt->execute($whereParams);
            $totalAnimais = (int)$countStmt->fetchColumn();
            $totalPaginas = max(1, (int)ceil($totalAnimais / $limite));

            $sql = "SELECT p.*,
                    EXISTS (
                        SELECT 1
                        FROM tb_favoritos f
                        WHERE f.id_pet = p.id
                            AND f.id_usuario = ?
                    ) AS favoritado
                FROM tb_pets p
                $where
                ORDER BY p.nome
                LIMIT $limite OFFSET $offset";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $animais = [];

            while ($dados = $stmt->fetch(PDO::FETCH_ASSOC)) {
              $fotos = $this->carregarFotos($dados['id']);
              $animal = new Animal(
                $dados['dono_id'],
                $dados['nome'],
                $dados['raca'],
                $dados['cor'],
                $dados['sexo'],
                $dados['tipo'],
                $dados['porte'],
                $dados['data_nascimento'],
                $dados['peso'],
                $dados['descricao'],
                $dados['vacinado'],
                $dados['certificado_raca'],
                $dados['foto_certificado'],
                $dados['foto_vacinas'],
                $dados['id'] ?? null,
                $dados['favoritado'] ?? 0,
                $fotos
              );
              $animais[] = $animal;
            }

            return [
                'animais' => $animais,
                'totalPaginas' => $totalPaginas,
                'paginaAtual' => $pagina
            ];
        }

        public function readAll($usuarioId, $pagina = 1) {
            $limite = 24;
            $offset = ($pagina - 1) * $limite;

            $countSql = "SELECT COUNT(*) FROM tb_pets WHERE dono_id != ?";
            $countStmt = $this->pdo->prepare($countSql);
            $countStmt->execute([$usuarioId]);
            $totalAnimais = (int)$countStmt->fetchColumn();
            $totalPaginas = max(1, (int)ceil($totalAnimais / $limite));

            $sql = "SELECT p.*, 
                    EXISTS (
                        SELECT 1
                        FROM tb_favoritos f
                        WHERE f.id_pet = p.id
                            AND f.id_usuario = ?
                    ) AS favoritado
                FROM tb_pets p
                WHERE p.dono_id != ?
                ORDER BY p.nome
                LIMIT $limite OFFSET $offset";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$usuarioId, $usuarioId]);
            $animais = [];

            while ($dados = $stmt->fetch(PDO::FETCH_ASSOC)) {
              $fotos = $this->carregarFotos($dados['id']);
              $animal = new Animal(
                $dados['dono_id'],
                $dados['nome'],
                $dados['raca'],
                $dados['cor'],
                $dados['sexo'],
                $dados['tipo'],
                $dados['porte'],
                $dados['data_nascimento'],
                $dados['peso'],
                $dados['descricao'],
                $dados['vacinado'],
                $dados['certificado_raca'],
                $dados['foto_certificado'],
                $dados['foto_vacinas'],
                $dados['id'] ?? null,
                $dados['favoritado'] ?? 0,
                $fotos
              );
              $animais[] = $animal;
            }

            return [
                'animais' => $animais,
                'totalPaginas' => $totalPaginas,
                'paginaAtual' => $pagina
            ];
          }

        public function readByDonoId($donoId) {
            $sql = "SELECT * FROM tb_pets WHERE dono_id = ? ORDER BY nome";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$donoId]);
            $animais = [];
        
            while ($dados = $stmt->fetch(PDO::FETCH_ASSOC)) {
              $fotos = $this->carregarFotos($dados['id']);
              $animal = new Animal(
                $dados['dono_id'],
                $dados['nome'],
                $dados['raca'],
                $dados['cor'],
                $dados['sexo'],
                $dados['tipo'],
                $dados['porte'],
                $dados['data_nascimento'],
                $dados['peso'],
                $dados['descricao'],
                $dados['vacinado'],
                $dados['certificado_raca'],
                $dados['foto_certificado'],
                $dados['foto_vacinas'],
                $dados['id'] ?? null,
                false,
                $fotos
              );
              $animal->setId($dados['id']);
              $animais[] = $animal;
            }
            
            return $animais;
        }

        public function adicionarFoto($petId, $fotoPath) {
            $sqlMax = "SELECT COALESCE(MAX(ordem), -1) + 1 FROM tb_pets_fotos WHERE pet_id = ?";
            $stmt = $this->pdo->prepare($sqlMax);
            $stmt->execute([$petId]);
            $ordem = (int)$stmt->fetchColumn();

            $sql = "INSERT INTO tb_pets_fotos (pet_id, foto_path, ordem) VALUES (?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$petId, $fotoPath, $ordem]);
        }

        public function removerFoto($fotoId) {
            $sql = "SELECT foto_path, pet_id FROM tb_pets_fotos WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$fotoId]);
            $foto = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$foto) return null;

            $arquivo = __DIR__ . "/../../uploads/animais/" . $foto['foto_path'];
            if (file_exists($arquivo)) {
                unlink($arquivo);
            }

            $sqlDel = "DELETE FROM tb_pets_fotos WHERE id = ?";
            $stmt = $this->pdo->prepare($sqlDel);
            $stmt->execute([$fotoId]);

            return $foto['pet_id'];
        }

        public function removerFotoByPath($petId, $fotoPath) {
            $fotoPath = basename($fotoPath);
            $sql = "SELECT id FROM tb_pets_fotos WHERE pet_id = ? AND foto_path = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$petId, $fotoPath]);
            $foto = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$foto) return false;

            $arquivo = __DIR__ . "/../../uploads/animais/" . $fotoPath;
            if (file_exists($arquivo)) {
                unlink($arquivo);
            }

            $stmtDel = $this->pdo->prepare("DELETE FROM tb_pets_fotos WHERE id = ?");
            $stmtDel->execute([$foto['id']]);
            return true;
        }

        public function atualizarAnimal(Animal $animal) {
            $sql = "UPDATE tb_pets SET nome=?, raca=?, cor=?, sexo=?, tipo=?, porte=?, data_nascimento=?, peso=?, descricao=?, vacinado=?, certificado_raca=?, foto_certificado=?, foto_vacinas=? WHERE id=? AND dono_id=?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                $animal->getNome(),
                $animal->getRaca(),
                $animal->getCor(),
                $animal->getSexo(),
                $animal->getTipo(),
                $animal->getPorte(),
                $animal->getDataNascimento(),
                $animal->getPeso(),
                $animal->getDescricao(),
                $animal->getVacinado(),
                $animal->getCertificado(),
                $animal->getFotoCertificado(),
                $animal->getFotoVacinas(),
                $animal->getId(),
                $animal->getDonoId()
            ]);
        }

        public function delete($id) {
            $pdo = Conexao::getConexao();

            $arquivo = __DIR__ . "/../../uploads/animais/";

            $sqlFotos = "SELECT foto_path FROM tb_pets_fotos WHERE pet_id = ?";
            $stmtFotos = $pdo->prepare($sqlFotos);
            $stmtFotos->execute([$id]);
            while ($foto = $stmtFotos->fetch(PDO::FETCH_ASSOC)) {
                if (
                    !empty($foto['foto_path']) &&
                    $foto['foto_path'] !== 'placeholder.webp' &&
                    file_exists($arquivo . $foto['foto_path'])
                ) {
                    unlink($arquivo . $foto['foto_path']);
                }
            }

            $sql = "SELECT * FROM tb_pets WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);
            $animal = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($animal) {
                if (
                    !empty($animal['foto_certificado']) &&
                    $animal['foto_certificado'] !== 'placeholder.webp' &&
                    file_exists($arquivo . $animal['foto_certificado'])
                ) {
                    unlink($arquivo . $animal['foto_certificado']);
                }
                if (
                    !empty($animal['foto_vacinas']) &&
                    $animal['foto_vacinas'] !== 'placeholder.webp' &&
                    file_exists($arquivo . $animal['foto_vacinas'])
                ) {
                    unlink($arquivo . $animal['foto_vacinas']);
                }

                $sql = "DELETE FROM tb_pets WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$id]);
            }
        }
    }