<?php

class Mensagem implements JsonSerializable {
    private $id;
    private $conversa_id;
    private $remetente_id;
    private $conteudo;
    private $data_envio;

    public function __construct($conversa_id, $remetente_id, $conteudo, $data_envio = null, $id = null) {
        $this->conversa_id = (int) $conversa_id;
        $this->remetente_id = (int) $remetente_id;
        $this->conteudo = $conteudo;
        $this->data_envio = $data_envio;
        $this->id = $id ? (int) $id : null;
    }

    public function getId() { return $this->id; }
    public function getConversaId() { return $this->conversa_id; }
    public function getRemetenteId() { return $this->remetente_id; }
    public function getConteudo() { return $this->conteudo; }
    public function getDataEnvio() { return $this->data_envio; }
    public function setDataEnvio($data_envio) { $this->data_envio = $data_envio; }

    public function setId($id) {
        if (!filter_var($id, FILTER_VALIDATE_INT) || $id <= 0) {
            throw new Exception("ID inválido.");
        }
        $this->id = (int) $id;
    }

    public function jsonSerialize(): array {
        return [
            'id' => $this->id,
            'conversa_id' => $this->conversa_id,
            'remetente_id' => $this->remetente_id,
            'conteudo' => $this->conteudo,
            'data_envio' => $this->data_envio
        ];
    }
}
