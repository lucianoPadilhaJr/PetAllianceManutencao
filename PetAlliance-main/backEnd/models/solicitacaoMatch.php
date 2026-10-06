<?php

class SolicitacaoMatch implements JsonSerializable {
    private $id;
    private $pet_id;
    private $remetente_id;
    private $status;
    private $criado_em;

    public function __construct($pet_id, $remetente_id, $status = 'pendente', $id = null, $criado_em = null) {
        $this->pet_id = (int) $pet_id;
        $this->remetente_id = (int) $remetente_id;
        $this->status = $status;
        $this->id = $id ? (int) $id : null;
        $this->criado_em = $criado_em;
    }

    public function getId() { return $this->id; }
    public function getPetId() { return $this->pet_id; }
    public function getRemetenteId() { return $this->remetente_id; }
    public function getStatus() { return $this->status; }
    public function getCriadoEm() { return $this->criado_em; }

    public function setId($id) {
        if (!filter_var($id, FILTER_VALIDATE_INT) || $id <= 0) {
            throw new Exception("ID inválido.");
        }
        $this->id = (int) $id;
    }

    public function setStatus($status) {
        if (!in_array($status, ['pendente', 'aceito', 'recusado'], true)) {
            throw new Exception("Status inválido.");
        }
        $this->status = $status;
    }

    public function jsonSerialize(): array {
        return [
            'id' => $this->id,
            'pet_id' => $this->pet_id,
            'remetente_id' => $this->remetente_id,
            'status' => $this->status,
            'criado_em' => $this->criado_em
        ];
    }
}
