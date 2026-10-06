CREATE DATABASE IF NOT EXISTS pet_alliance_db;
USE pet_alliance_db;

-- USUÁRIOS
CREATE TABLE tb_usuarios (
	id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
	foto_perfil VARCHAR(250) DEFAULT"placeholder.webp",
    cep varchar(8) NOT NULL,
    cpf CHAR(11) UNIQUE NOT NULL,
    tipo_usuario INT DEFAULT 0 NOT NULL,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    email_pendente VARCHAR(100) NULL,
    token_email_pendente VARCHAR(255) NULL,
    senha VARCHAR(255) NOT NULL,
    verificado BOOLEAN DEFAULT FALSE,
    tentativas_login INT DEFAULT 0 ,
    bloqueado BOOLEAN DEFAULT FALSE,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL
);

-- PETS
CREATE TABLE tb_pets (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    dono_id BIGINT UNSIGNED NOT NULL,
    nome VARCHAR(50) NOT NULL,
    raca VARCHAR(50),
    cor VARCHAR(30),
    sexo VARCHAR(10) NOT NULL,
    tipo VARCHAR(30) NOT NULL,
    porte VARCHAR(20),
    data_nascimento DATE,
    peso DECIMAL(5,2),
    descricao TEXT,
    vacinado BOOLEAN NOT NULL,
    foto_vacinas VARCHAR(250) DEFAULT 0,
    certificado_raca BOOLEAN DEFAULT FALSE,
    foto_certificado VARCHAR(250) DEFAULT 0,
    venda_preco DECIMAL(10,2) DEFAULT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ,
    FOREIGN KEY (dono_id) REFERENCES tb_usuarios(id)
    ON DELETE CASCADE
);

-- MATCH
CREATE TABLE tb_matches (
    id SERIAL PRIMARY KEY, 
    id_usuario1 BIGINT UNSIGNED NOT NULL,  
    id_animal1 BIGINT UNSIGNED NOT NULL,   
    id_usuario2 BIGINT UNSIGNED NOT NULL,  
    id_animal2 BIGINT UNSIGNED NOT NULL,   
    aceito BOOLEAN DEFAULT FALSE,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    respondido_em TIMESTAMP NULL DEFAULT NULL,
    CONSTRAINT fk_usuario1 FOREIGN KEY (id_usuario1) REFERENCES tb_usuarios(id),
    CONSTRAINT fk_animal1 FOREIGN KEY (id_animal1) REFERENCES tb_pets(id),
    CONSTRAINT fk_usuario2 FOREIGN KEY (id_usuario2) REFERENCES tb_usuarios(id),
    CONSTRAINT fk_animal2 FOREIGN KEY (id_animal2) REFERENCES tb_pets(id),
    CONSTRAINT uq_match_animais UNIQUE (id_animal1, id_animal2)
);
-- BLOQUEIOS
CREATE TABLE tb_bloqueios (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id BIGINT UNSIGNED NOT NULL,
    bloqueado_id BIGINT UNSIGNED NOT NULL,
    CONSTRAINT fk_bloqueios_usuario FOREIGN KEY (usuario_id) REFERENCES tb_usuarios(id) ON DELETE CASCADE,
    CONSTRAINT fk_bloqueios_bloqueado FOREIGN KEY (bloqueado_id) REFERENCES tb_usuarios(id) ON DELETE CASCADE
);

-- SOLICITAÇÕES DE MATCH
CREATE TABLE tb_solicitacoes_match (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pet_id BIGINT UNSIGNED NOT NULL,
    remetente_id BIGINT UNSIGNED NOT NULL,
    status ENUM('pendente', 'aceito', 'recusado') DEFAULT 'pendente' NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
    CONSTRAINT fk_solicitacoes_pet FOREIGN KEY (pet_id) REFERENCES tb_pets(id) ON DELETE CASCADE,
    CONSTRAINT fk_solicitacoes_remetente FOREIGN KEY (remetente_id) REFERENCES tb_usuarios(id) ON DELETE CASCADE
);

-- CHAT (CONVERSA)
CREATE TABLE tb_conversas (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY ,
    solicitacao_id BIGINT UNSIGNED NOT NULL,
    ativa BOOLEAN DEFAULT TRUE NOT NULL,
    CONSTRAINT fk_conversas_solicitacao FOREIGN KEY (solicitacao_id) REFERENCES tb_solicitacoes_match(id) ON DELETE CASCADE
);

-- MENSAGENS
CREATE TABLE tb_mensagens (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY ,
    conversa_id BIGINT UNSIGNED NOT NULL,
    remetente_id BIGINT UNSIGNED NOT NULL,
    conteudo VARCHAR(500) NOT NULL,
    lida TINYINT(1) DEFAULT 0,
    data_envio TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
    CONSTRAINT fk_mensagens_conversa FOREIGN KEY (conversa_id) REFERENCES tb_conversas(id) ON DELETE CASCADE,
    CONSTRAINT fk_mensagens_remetente FOREIGN KEY (remetente_id) REFERENCES tb_usuarios(id) ON DELETE CASCADE
);

-- FAVORITOS
CREATE TABLE tb_favoritos (
	id_pet BIGINT UNSIGNED,
    id_usuario BIGINT UNSIGNED,
    PRIMARY KEY (id_usuario, id_pet),
    CONSTRAINT fk_favoritos_usuario FOREIGN KEY (id_usuario) REFERENCES tb_usuarios(id) ON DELETE CASCADE,
    CONSTRAINT fk_favoritos_pet FOREIGN KEY (id_pet) REFERENCES tb_pets(id) ON DELETE CASCADE
);

-- DENÚNCIAS
CREATE TABLE tb_denuncias (
     id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
     usuario_id BIGINT UNSIGNED NULL,
     tipo_alvo ENUM('animal', 'usuario', 'site') NOT NULL,
     alvo_id BIGINT UNSIGNED NULL,
     descricao TEXT NOT NULL,
     resolvido TINYINT(1) NOT NULL DEFAULT 0,
     criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
     CONSTRAINT fk_denuncias_usuario FOREIGN KEY (usuario_id) REFERENCES tb_usuarios(id) ON DELETE SET NULL
 );

-- NOTIFICAÇÕES
CREATE TABLE tb_notificacoes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id BIGINT UNSIGNED NOT NULL,
    tipo VARCHAR(50) NOT NULL,
    mensagem TEXT NOT NULL,
    lida TINYINT(1) DEFAULT 0 NOT NULL,
    link VARCHAR(255) DEFAULT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
    CONSTRAINT fk_notificacoes_usuario FOREIGN KEY (usuario_id) REFERENCES tb_usuarios(id) ON DELETE CASCADE
);

-- PAGAMENTOS
CREATE TABLE tb_pagamentos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY ,
    usuario_id BIGINT UNSIGNED NULL,
    prod_id_abacatepay varchar(200) NOT NULL,
    status_pagamento ENUM('PENDING', 'PAID', 'REFUNDED', 'EXPIRED', 'CANCELLED') NOT NULL DEFAULT 'PENDING',
    id_transacao_abacatepay VARCHAR(255) UNIQUE NULL,
    valor DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    plano_nome VARCHAR(30) NULL,
    plano_max_destaques INT NULL,
    plano_expiracao DATE NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_pagamentos_usuario FOREIGN KEY (usuario_id) REFERENCES tb_usuarios(id) ON DELETE SET NULL
);

-- PETS DESTACADOS POR MEMBROS
CREATE TABLE tb_pets_membros (
    usuario_id BIGINT UNSIGNED NOT NULL,
    animal_id BIGINT UNSIGNED NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (usuario_id, animal_id),
    FOREIGN KEY (usuario_id) REFERENCES tb_usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (animal_id) REFERENCES tb_pets(id) ON DELETE CASCADE
);

CREATE TABLE tb_pets_fotos (
  id bigint unsigned NOT NULL AUTO_INCREMENT,
  pet_id bigint unsigned NOT NULL,
  foto_path varchar(250) NOT NULL,
  ordem int DEFAULT '0',
  criado_em timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY fk_fotos_pet (pet_id),
  CONSTRAINT fk_fotos_pet FOREIGN KEY (pet_id) REFERENCES tb_pets (id) ON DELETE CASCADE
)

-- VENDAS
CREATE TABLE tb_vendas (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    animal_id BIGINT UNSIGNED NULL,
    comprador_id BIGINT UNSIGNED NULL,
    vendedor_id BIGINT UNSIGNED NULL,
    preco DECIMAL(10,2) NOT NULL,
    status ENUM('PENDING', 'PAID', 'SHIPPED', 'DELIVERED', 'CANCELLED') NOT NULL DEFAULT 'PENDING',
    pagamento_id BIGINT UNSIGNED NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_vendas_animal FOREIGN KEY (animal_id) REFERENCES tb_pets(id) ON DELETE SET NULL,
    CONSTRAINT fk_vendas_comprador FOREIGN KEY (comprador_id) REFERENCES tb_usuarios(id) ON DELETE SET NULL,
    CONSTRAINT fk_vendas_vendedor FOREIGN KEY (vendedor_id) REFERENCES tb_usuarios(id) ON DELETE SET NULL,
    CONSTRAINT fk_vendas_pagamento FOREIGN KEY (pagamento_id) REFERENCES tb_pagamentos(id) ON DELETE SET NULL
);

-- VERIFICAÇÃO DE EMAIL E RECUPERAÇÃO DE SENHA
CREATE TABLE tb_verificacao_email (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    usuario_id BIGINT UNSIGNED NOT NULL,
    token VARCHAR(255) NOT NULL,
    codigo VARCHAR(6) NOT NULL,
    tipo ENUM('verificacao', 'recuperacao', 'alteracao_email_atual', 'alteracao_email_novo') NOT NULL DEFAULT 'verificacao',
    expiracao DATETIME NOT NULL,
    usado BOOLEAN DEFAULT FALSE,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_verificacao_email_usuario FOREIGN KEY (usuario_id) REFERENCES tb_usuarios(id) ON DELETE CASCADE
);
