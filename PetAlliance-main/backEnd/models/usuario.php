<?php

class Usuario implements JsonSerializable {
    private $imagem;
    private $cpf;
    private $cep;
    private $tipo;
    private $nome;
    private $email;
    private $senha;
    private $id;

    public function __construct($imagem, $cpf, $cep,$tipo, $nome, $email, $senha, $id = null) {
        $this->imagem = $imagem;
        $this->cpf = $cpf;
        $this->cep = $cep;
        $this->tipo = $tipo;
        $this->nome = $nome;
        $this->email = $email;
        $this->senha = $senha;
        $this->id = $id;
    }

    public function getImagem() {
        return $this->imagem;
    }

    public function getId() {
        return $this->id;
    }

    public function getCpf() {
        return $this->cpf;
    }

    public function getCep() {
        return $this->cep;
    }
    public function getTipo() {
        return $this->tipo;
    }
    public function getNome() {
        return $this->nome;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getSenha() {
        return $this->senha;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setImagem($imagem) {
        $this->imagem = $imagem;
    }

    public function setCpf($cpf) {
        if (empty($cpf)) {
          throw new Exception("Cpf não pode ser vazio");
        }
        $this->cpf = $cpf;
    }

    public function setCep($cep) {
        $this->cep = $cep;
    }
    public function setTipo($tipo) {
        $this->tipo = $tipo;
    }

    public function setNome($nome) {
        if (mb_strlen($nome) > 100) {
            throw new InvalidArgumentException("Nome deve ter no máximo 100 caracteres.");
        }
        $this->nome = $nome;
    }

    public function setEmail($email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Email inválido.");
        }
        if (mb_strlen($email) > 100) {
            throw new InvalidArgumentException("Email deve ter no máximo 100 caracteres.");
        }
        $this->email = $email;
    }

    public function setSenha($senha) {
        $this->senha = $senha;
    }

    public function jsonSerialize(): array {
        return [
            'imagem' => $this->imagem,
            'cpf' => $this->cpf,
            'cep' => $this->cep,
            'tipo' => $this->tipo,
            'nome' => $this->nome,
            'email' => $this->email,
            'id' => $this->id
        ];
    }
}