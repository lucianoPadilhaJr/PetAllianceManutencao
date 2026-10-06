<?php

class Animal implements JsonSerializable {
    private $dono_id;
    private $nome;
    private $raca;
    private $cor;
    private $sexo;
    private $tipo;
    private $porte;
    private $data_nascimento;
    private $peso;
    private $descricao;
    private $vacinado;
    private $certificado;
    private $foto_certificado;
    private $foto_vacina;

    private $id;
    private $favoritado;
    private $fotos = [];

    public function __construct($dono_id, $nome, $raca, $cor, $sexo, $tipo, $porte, $data_nascimento, $peso, $descricao, $vacinado, $certificado, $foto_certificado, $foto_vacina, $id = null, $favoritado = false, $fotos = []
    ) {
        $this->dono_id = $dono_id;
        $this->nome = $nome;
        $this->raca = $raca;
        $this->cor = $cor;
        $this->sexo = $sexo;
        $this->tipo = $tipo;
        $this->porte = $porte;
        $this->data_nascimento = $data_nascimento;
        $this->peso = $peso;
        $this->descricao = $descricao;
        $this->vacinado = $vacinado;
        $this->certificado = $certificado;
        $this->foto_certificado =$foto_certificado;
        $this->foto_vacina = $foto_vacina;
        $this->id = $id;
        $this->favoritado = (bool) $favoritado;
        $this->fotos = $fotos;
    }


    public function getId() {
        return $this->id;
    }

    public function getDonoid() {
        return $this->dono_id;
    }

    public function getFotos() {
        return $this->fotos;
    }

    public function setFotos($fotos) {
        $this->fotos = $fotos;
    }

    public function getFotoPet() {
        return $this->fotos[0] ?? 'placeholder.webp';
    }

    public function getNome() {
        return $this->nome;
    }

    public function setNome($nome) {
        if (mb_strlen($nome) > 50) {
            throw new Exception("Nome deve ter no máximo 50 caracteres.");
        }
        $this->nome = $nome;
    }

    public function getRaca() {
        return $this->raca;
    }

    public function getCor() {
        return $this->cor;
    }

    public function setCor($cor) {
        $permitidos = ["Preto", "Branco", "Cinza", "Marrom", "Outro"];
        if (!in_array($cor, $permitidos, true)) {
            throw new Exception("Cor inválida. Selecione: Preto, Branco, Cinza, Marrom ou Outro.");
        }
        $this->cor = $cor;
    }

    public function getSexo() {
        return $this->sexo;
    }

    public function getTipo() {
        return $this->tipo;
    }

    public function getPorte() {
        return $this->porte;
    }

    public function getDataNascimento() {
        return $this->data_nascimento;
    }

    public function getPeso() {
        return $this->peso;
    }

    public function getDescricao() {
        return $this->descricao;
    }

    public function getVacinado() {
        return $this->vacinado;
    }

    public function getCertificado() {
        return $this->certificado;
    }

    public function getFotoCertificado() {
        return $this->foto_certificado;
    }

    public function getFotoVacinas() {
        return $this->foto_vacina;
    }

    public function setId($id) {
        if (!filter_var($id, FILTER_VALIDATE_INT) || $id <= 0) {
            throw new Exception("ID inválido.");
        }
    
        $this->id = (int) $id;
    }

    public function getFavoritado() {
        return $this->favoritado;
    }

    public function setFavoritado($favoritado) {
        $this->favoritado = (bool) $favoritado;
    }

    public function setDonoid($donoid) {
        if (!filter_var($donoid, FILTER_VALIDATE_INT) || $donoid <= 0) {
            throw new Exception("ID do dono inválido.");
        }
    
        $this->dono_id = (int) $donoid;
    }
    
    public function setRaca($raca) {
        $raca = trim($raca);

        if ($raca === '') {
            throw new Exception("Raça não pode ser vazia.");
        }
    
        if (mb_strlen($raca) > 80) {
            throw new Exception("Raça pode ter no máximo 80 caracteres.");
        }
        $this->raca = $raca;
    }

    public function setSexo($sexo) {
        if (!in_array($sexo, ["Macho", "Fêmea"], true)) {
            throw new Exception("Sexo inválido.");
        }
        $this->sexo = $sexo;
    }

    public function setTipo($tipo) {
        $permitidos = ["Cachorro", "Gato", "Cavalo", "Outro"];
        if (!in_array($tipo, $permitidos, true)) {
            throw new Exception("Tipo inválido. Selecione: Cachorro, Gato, Cavalo ou Outro.");
        }
        $this->tipo = $tipo;
    }

    public function setPorte($porte) {
        if (!in_array($porte, ["Pequeno", "Médio", "Grande"], true)) {
            throw new Exception("Porte inválido.");
        }
    
        $this->porte = $porte;
    }
    
    public function setDtNascimento($dt_nascimento) {
        if (empty($dt_nascimento)) {
            throw new Exception("Data de nascimento não pode ser vazia.");
        }
    
        $data = DateTime::createFromFormat("Y-m-d", $dt_nascimento);
    
        if (!$data || $data->format("Y-m-d") !== $dt_nascimento) {
            throw new Exception("Data de nascimento inválida.");
        }

        if ($data > new DateTime()) {
            throw new Exception("A data de nascimento não pode ser futura.");
        }

        if ($dt_nascimento < '2000-01-01') {
            throw new Exception('A data escolhida é menor que a data mínima permitida.');
        }

        $this->data_nascimento = $dt_nascimento;
    }

    public function setPeso($peso) {
        if (!is_numeric($peso) || $peso <= 0 || $peso > 500) {
            throw new Exception("Peso deve ser entre 0.001 kg (1 grama) e 500 kg.");
        }
    
        $this->peso = $peso;
    }

     public function setDescricao($descricao) {
            if (empty($descricao)) {
                throw new Exception("Descrição não pode ser vazia.");
            }
        if (mb_strlen($descricao) > 2000) {
            throw new Exception("Descrição deve ter no máximo 2000 caracteres.");
        }
        $this->descricao = $descricao;
    }

    public function setVacinado($vacinado) {
        if (!in_array($vacinado, [0, 1, "0", "1"], true)) {
            throw new Exception("Valor de vacinação inválido.");
        }
    
        $this->vacinado = (int) $vacinado;
    }
    
    public function setCertificado($certificado) {
        if (!in_array($certificado, [0, 1, "0", "1"], true)) {
            throw new Exception("Valor de certificado inválido.");
        }
    
        $this->certificado = (int) $certificado;
    }

    public function setFotoCertificado($foto_certificado) {
        $this->foto_certificado = $foto_certificado;
        if (!empty($_FILES['arquivoCertificado']['name'])) {
                $extensao  = pathinfo($_FILES['arquivoCertificado']['name'], PATHINFO_EXTENSION);
                $permitidos = ['jpg', 'jpeg', 'png', 'webp'];
            
                if (!in_array(strtolower($extensao), $permitidos) || !validarMimeImagem($_FILES['arquivoCertificado']['tmp_name'])) {
                    $erro = 'Tipo de imagem não permitido.';
                } else {
                    $this->foto_certificado = uniqid('cert_') . '.' . $extensao;
                    move_uploaded_file($_FILES['arquivoCertificado']['tmp_name'], '../../uploads/animais/' . $this->foto_certificado);
                }
        }
            
    }

    public function setFotoVacina($foto_vacina) {
        $this->foto_vacina = $foto_vacina;
        if (!empty($_FILES['arquivoVacinacao']['name'])) {
                $extensao  = pathinfo($_FILES['arquivoVacinacao']['name'], PATHINFO_EXTENSION);
                $permitidos = ['jpg', 'jpeg', 'png', 'webp'];
            
                if (!in_array(strtolower($extensao), $permitidos) || !validarMimeImagem($_FILES['arquivoVacinacao']['tmp_name'])) {
                    $erro = 'Tipo de imagem não permitido.';
                } else {
                    $this->foto_vacina = uniqid('vaci_') . '.' . $extensao;
                    move_uploaded_file($_FILES['arquivoVacinacao']['tmp_name'], '../../uploads/animais/' . $this->foto_vacina);
                }
            }
        }

    public function jsonSerialize(): array {
        return [
            'id' => $this->id,
            'dono_id' => $this->dono_id,
            'foto_pet' => $this->fotos[0] ?? 'placeholder.webp',
            'nome' => $this->nome,
            'raca' => $this->raca,
            'cor' => $this->cor,
            'sexo' => $this->sexo,
            'tipo' => $this->tipo,
            'porte' => $this->porte,
            'data_nascimento' => $this->data_nascimento,
            'peso' => $this->peso,
            'descricao' => $this->descricao,
            'vacinado' => $this->vacinado,
            'certificado' => $this->certificado,
            'foto_certificado' => $this->foto_certificado,
            'foto_vacina' => $this->foto_vacina,
            'favoritado' => $this->favoritado,
            'fotos' => $this->fotos,
        ];
    }
}