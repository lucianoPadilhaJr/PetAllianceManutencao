<?php
    require_once __DIR__ . "/../../config/config.php";
    require_once __DIR__ . "/../../models/animal.php";
    require_once __DIR__ . "/../../models/animalDAO.php";

    class AnimalController {
        private $dao;

        public function __construct() {
            $this->dao = new AnimalDAO();
        }

        public function read($id, $usuarioId = null) {
            $animal = $this->dao->read($id, $usuarioId);
            echo json_encode($animal);
        }

        public function listarAnimais($id, $pagina = 1) {
            $resultado = $this->dao->readAll($id, $pagina);
            echo json_encode($resultado);
        }

        public function readByDonoId($donoId) {
            $animais = $this->dao->readByDonoId($donoId);
            echo json_encode($animais);
        }

        public function listarAnimaisFavoritos($id) {
            $favoritos = $this->dao->listarFavoritos($id);
            echo json_encode($favoritos);
        }

        public function favoritarAnimal($usuarioId, $petId) {
            $dados = json_decode(file_get_contents("php://input"), true);
            try {
                $animalFavoritado = $this->dao->favoritarAnimal($usuarioId, $petId);
                echo json_encode($animalFavoritado);
            } catch (Exception $e) {
                echo json_encode(["erro" => "Erro ao favoritar animal: " . $e->getMessage()]);
            }
        }

        public function buscarAnimais($termo, $usuarioId, $filtros = [], $pagina = 1) {
            $resultado = $this->dao->search($termo, $usuarioId, $filtros, $pagina);
            echo json_encode($resultado);
        }

        public function criarAnimal(Animal $animal) {
            try {
                return $this->dao->cadastrarAnimal($animal);
            } catch (InvalidArgumentException $e) {
                throw $e;
            } catch (Exception $e) {
                throw $e;
            }
        }
        public function deletarAnimal($id) {
            $this->dao->delete($id);
        
            echo json_encode([
                "sucesso" => true,
                "mensagem" => "Animal excluído com sucesso."
            ]);
        }
    }