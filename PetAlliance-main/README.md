# PetAlliance

Plataforma de match genetico que conecta criadores de animais de raca para cruzamento seletivo.

## Sobre

O **PetAlliance** conecta donos de animais interessados em cruzamento seletivo, facilitando o encontro de parceiros da mesma raca. Criadores cadastram seus animais com informacoes geneticas, fotos e disponibilidade, e a plataforma faz o match entre interessados.

## Funcionalidades

- Cadastro de animais com fotos e dados geneticos
- Match entre criadores da mesma raca
- Chat para combinacao de cruzamento
- Planos de assinatura com beneficios (Iniciante, Basico, Profissional, Premium)
- Badge de membro verificado e destaque de animais
- LGPD compliance com aceite de termos e politica de privacidade

## Tecnologias

- **Frontend:** HTML5 + CSS3 + JavaScript (Vanilla)
- **Backend:** PHP 8.3+ com PDO
- **Banco de Dados:** MySQL 5.7+
- **Pagamentos:** AbacatePay (API REST)
- **Autenticacao:** Sessions PHP
- **Estilizacao:** CSS puro com variaveis customizadas (paleta verde)
- **Hospedagem:** Servidor Apache com modulo mod_rewrite

## Ferramentas

- **VS Code** — editor principal
- **Git & GitHub** — versionamento
- **XAMPP** — ambiente de desenvolvimento local
- **Postman / Insomnia** — testes de API
- **phpMyAdmin** — gerenciamento do banco
- **WebP** — formato de imagens

## Como Rodar

### Pre-requisitos

- XAMPP / WAMP / LAMP instalado
- PHP >= 8.3
- MySQL >= 5.7
- Git
- Navegador moderno (Chrome, Firefox, Edge)

### Instalacao

```bash
# Clone o repositorio
git clone https://github.com/ThurStelzner/PetAlliance.git

# Entre na pasta
cd PetAlliance
```

1. Mova a pasta para o diretorio do servidor local (ex: `C:\xampp\htdocs\PetAlliance`)
2. Inicie o Apache e o MySQL no XAMPP
3. Acesse `http://localhost/phpmyadmin` e crie o banco de dados
4. Importe o script SQL (disponivel com a equipe)
5. Configure as credenciais do banco em `backEnd/config/config.php`:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'petalliance');
```

6. Configure a URL base e credenciais do AbacatePay no mesmo arquivo
7. Acesse no navegador: `http://localhost/PetAlliance`

> O banco de dados e privado da equipe. Solicite o dump SQL a um membro do time.

### Estrutura de Diretorios

```
PetAlliance/
├── backEnd/
│   ├── config/         # Configuracoes do banco e pagamento
│   ├── controllers/    # Logica da API (api/)
│   ├── models/         # Classes e DAOs
│   ├── views/          # Componentes PHP (navbar, membros)
│   ├── admin/          # Paginas PHP de administracao
│   ├── animal/         # Paginas PHP de animais
│   ├── denuncias/      # Paginas PHP de denuncias
│   ├── usuario/        # Paginas PHP de usuario
│   └── *.php           # Paginas/APIs principais (home, match, chat, pagamento)
├── frontEnd/
│   ├── assets/         # Imagens e logos
│   ├── style/          # CSS (style.css, estilos.css, chat.css)
│   ├── utils/          # JavaScript (appUsuario, chat, formatacao)
│   └── views/          # Fragmentos HTML
├── docs/               # Documentacao do projeto (SRS, testes, DER, planos)
├── tests/              # Testes unitarios (PHPUnit)
├── uploads/            # Fotos de usuarios e animais
├── vendor/             # Dependencias do Composer (PHPMailer)
└── index.php           # Landing page
```

## Planos

| Plano | Preco | Beneficios |
|---|---|---|
| Iniciante | R$ 7,50/mes | Badge de membro, 5 destaques |
| Basico | R$ 15/mes | Badge, perfil verificado, 10 destaques |
| Profissional | R$ 30/mes | Badge, verificado, 20 destaques, suporte |
| Premium | R$ 45/mes | Badge, verificado, 30 destaques, suporte VIP |

Cadastro e matches sao gratuitos e ilimitados para todos.
