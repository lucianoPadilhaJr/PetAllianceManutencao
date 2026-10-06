<?php

class Notificacao implements JsonSerializable {
    private $id;
    private $usuario_id;
    private $tipo;
    private $mensagem;
    private $lida;
    private $link;
    private $criado_em;

    public function __construct($usuario_id, $tipo, $mensagem, $link = null, $lida = 0, $id = null, $criado_em = null) {
        $this->usuario_id = (int) $usuario_id;
        $this->tipo = $tipo;
        $this->mensagem = $mensagem;
        $this->link = $link;
        $this->lida = (int) $lida;
        $this->id = $id ? (int) $id : null;
        $this->criado_em = $criado_em;
    }

    public function getId() { return $this->id; }
    public function getUsuarioId() { return $this->usuario_id; }
    public function getTipo() { return $this->tipo; }
    public function getMensagem() { return $this->mensagem; }
    public function getLida() { return $this->lida; }
    public function getLink() { return $this->link; }
    public function getCriadoEm() { return $this->criado_em; }

    public function setLida($lida) {
        $this->lida = (int) (bool) $lida;
    }

    public function jsonSerialize(): array {
        return [
            'id' => $this->id,
            'usuario_id' => $this->usuario_id,
            'tipo' => $this->tipo,
            'mensagem' => $this->mensagem,
            'lida' => $this->lida,
            'link' => $this->link,
            'criado_em' => $this->criado_em
        ];
    }
}
