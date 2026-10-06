<?php 

class Denuncia {

    private $id;
    private $usuario_id;
    private $tipo_alvo; // animal | usuario | site
    private $alvo_id;
    private $descricao;
    private $resolvido;

    public function __construct(
        $usuario_id,
        $tipo_alvo,
        $descricao,
        $alvo_id = null,
        $resolvido = 0,
        $id = null
    ) {
        $this->usuario_id = (int) $usuario_id;
        $this->tipo_alvo = $tipo_alvo;
        $this->alvo_id = $alvo_id !== null ? (int) $alvo_id : null;
        $this->descricao = $descricao;
        $this->resolvido = (int) $resolvido;
        $this->id = $id;
    }

    public function getId() { return $this->id; }
    public function getUsuarioId() { return $this->usuario_id; }
    public function getTipoAlvo() { return $this->tipo_alvo; }
    public function getAlvoId() { return $this->alvo_id; }
    public function getDescricao() { return $this->descricao; }
    public function getResolvido() { return $this->resolvido; }

    public function setUsuarioId($usuario_id) {
        if (!filter_var($usuario_id, FILTER_VALIDATE_INT) || $usuario_id <= 0) {
            throw new Exception("Usuário inválido.");
        }
    
        $this->usuario_id = (int) $usuario_id;
    }
    public function setTipoAlvo($tipo_alvo) {
        $tipos = ['animal', 'usuario', 'site'];
    
        if (!in_array($tipo_alvo, $tipos, true)) {
            throw new Exception("Tipo de denúncia inválido.");
        }
    
        $this->tipo_alvo = $tipo_alvo;
    }
    public function setAlvoId($alvo_id) {
        if ($alvo_id === null || $alvo_id === '') {
            $this->alvo_id = null;
            return;
        }
    
        if (!filter_var($alvo_id, FILTER_VALIDATE_INT) || $alvo_id <= 0) {
            throw new Exception("Alvo inválido.");
        }
    
        $this->alvo_id = (int) $alvo_id;
    }
    public function setDescricao($descricao) {
        $descricao = trim($descricao);
    
        if ($descricao === '') {
            throw new Exception("Descrição não pode ser vazia.");
        }
    
        if (mb_strlen($descricao) > 1000) {
            throw new Exception("Descrição muito longa.");
        }
    
        $this->descricao = $descricao;
    }
    public function setResolvido($resolvido) {
        $this->resolvido = (int) (bool) $resolvido;
    }
    }
    