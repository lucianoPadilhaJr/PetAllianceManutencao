# Checklist de IdentificaÃ§Ã£o TecnolÃ³gica do Sistema Desenvolvido na SA

| Nome dos alunos | Turma | Data | Sistema analisado |
|---|---|---|---|
| Carlos Eduardo Duhring / Arthur Iantas Stelzner / Alexandre Gulini | DSV2 | 04/08/2026 | Pet Alliance |

---

## Modelagem de Sistemas

| Item | Status |
|---|---|
| Diagramas de casos de uso (UML) identificados e mapeados | X |
| Diagrama de classes (entidades, atributos, mÃ©todos, relacionamentos) | X |
| Diagrama de sequÃªncia e/ou atividades para fluxos crÃ­ticos | â˜ |
| PadrÃ£o arquitetural identificado (MVC, MVVM, camadas, microsserviÃ§os, monolito) | X |
| DocumentaÃ§Ã£o de requisitos funcionais e nÃ£o funcionais | X |
| Regras de negÃ³cio mapeadas e localizadas no cÃ³digo | X |

ObservaÃ§Ãµes: ____________________________________________________________

- **Casos de uso:** mapeados em `docs/user-stories.md` (US01â€“US11 no formato "Como... quero... para...", com critÃ©rios de aceite), `docs/cenarios-bdd.md` (cenÃ¡rios Gherkin) e `docs/matriz-rastreabilidade.md` (RF â†” US â†” casos de teste â€” 48 CTs, 100% dos RFs e RNFs com ao menos 1 CT).
- **Diagrama de classes:** as entidades estÃ£o modeladas em `backEnd/models/` â€” classes `Usuario`, `Animal`, `Denuncia`, `Mensagem`, `Notificacao`, `SolicitacaoMatch` (atributos privados, getters/setters, `JsonSerializable`) + classes DAO correspondentes (`usuarioDAO`, `animalDAO`, `denunciaDAO`, `mensagemDAO`, `conversaDAO`, `solicitacaoMatchDAO`, `verificacaoDAO`, `notificacaoDAO`). PendÃªncia: nÃ£o hÃ¡ um diagrama de classes UML grÃ¡fico formal (ex.: .drawio/.png) â€” apenas a modelagem refletida no cÃ³digo e no DER.
- **Fluxos crÃ­ticos:** descritos textualmente em `docs/teste-de-mesa.md` (TM03 â€” mÃ¡quina de estados do match) e nos cenÃ¡rios BDD (match, pagamento, verificaÃ§Ã£o de e-mail, recuperaÃ§Ã£o de senha); diagramas de sequÃªncia/atividade formais pendentes.
- **PadrÃ£o arquitetural:** **MVC (arquitetura em camadas, monolito)** implementado de forma artesanal, sem framework: View (`frontEnd/views/*.html` + `backEnd/views/navBar.php`), Controller (`backEnd/controllers/api/*` retornando JSON), Model (`backEnd/models/*` + DAOs com PDO).
- **Requisitos:** SRS completo em `docs/requisitosFuncionais.txt` â€” 11 requisitos funcionais (RF01â€“RF11) e 6 nÃ£o funcionais (RNF01â€“RNF06).
- **Regras de negÃ³cio localizadas no cÃ³digo:** validaÃ§Ã£o de CPF (`validaCPF()` no `UsuarioDAO`), senha forte (`validarSenhaForte()` em `backEnd/config/validacao.php`), verificaÃ§Ã£o de e-mail obrigatÃ³ria para login, chat liberado somente apÃ³s match, ediÃ§Ã£o de pet restrita ao dono (RF03.5 â€” `animalDAO.php:433` com `AND dono_id=?`), permissÃ£o de admin (`tipo_usuario == 1`), validaÃ§Ã£o de MIME de imagens no upload (`validarMimeImagem()` em `config.php`).

---

## Modelagem de Banco de Dados

| Item | Status |
|---|---|
| Modelo conceitual (DER) com entidades e relacionamentos identificados | X |
| Modelo lÃ³gico (normalizaÃ§Ã£o, chaves primÃ¡rias/estrangeiras, cardinalidade) | X |
| Modelo fÃ­sico (tipos de dados, Ã­ndices, constraints, triggers, views, procedures) | X |
| Tipo de SGBD identificado (relacional ou NoSQL) | X |
| Mecanismos de integridade referencial e transaÃ§Ãµes (ACID) | X |
| EstratÃ©gia de backup, replicaÃ§Ã£o e versionamento de schema (migrations) | X |
| SeguranÃ§a de dados (criptografia, mascaramento, controle de acesso) | â˜ |

ObservaÃ§Ãµes: ____________________________________________________________

- **Modelo conceitual:** DER disponÃ­vel em `docs/DER-PetAlliance.pdf` (12 entidades: usuÃ¡rios, pets, matches, bloqueios, solicitaÃ§Ãµes de match, conversas, mensagens, favoritos, denÃºncias, notificaÃ§Ãµes, pagamentos, pets_membros, vendas, verificaÃ§Ã£o de e-mail).
- **Modelo lÃ³gico:** `docs/banco.sql` â€” chaves primÃ¡rias (PK), chaves estrangeiras nomeadas (`fk_*`) com cardinalidade definida, constraints `UNIQUE` (cpf, e-mail, `uq_match_animais`, `id_transacao_abacatepay`) e `ENUM` para status (match, denÃºncia, pagamento, venda).
- **Modelo fÃ­sico:** tipos definidos (BIGINT UNSIGNED, CHAR(11) para CPF, DECIMAL(10,2) para valores, DATE/TIMESTAMP, TINYINT/BOOLEAN), constraints `ON DELETE CASCADE` / `ON DELETE SET NULL` e `AUTO_INCREMENT`. PendÃªncia: sem triggers, views ou stored procedures (toda a lÃ³gica estÃ¡ na camada PHP).
- **SGBD:** **MySQL 5.7+ (relacional)** â€” conexÃ£o via PDO (`mysql:host=tini.click;dbname=pet_alliance_db`), gerenciado em dev pelo phpMyAdmin (XAMPP).
- **Integridade/ACID:** integridade referencial garantida por FKs e constraints; o InnoDB garante as propriedades ACID. PendÃªncia: nÃ£o hÃ¡ uso explÃ­cito de transaÃ§Ãµes (`BEGIN`/`COMMIT`/`ROLLBACK`) em fluxos multi-tabela (match, pagamento) â€” recomendaÃ§Ã£o de melhoria.
- **Versionamento de schema:** `docs/banco.sql` versionado no Git (migraÃ§Ã£o manual â€” nÃ£o hÃ¡ ferramenta de migrations como Flyway/Laravel); RNF05/RNF06 preveem backup diÃ¡rio automÃ¡tico e disponibilidade 24/7; sem replicaÃ§Ã£o configurada.
- **SeguranÃ§a de dados (pendente):** senhas criptografadas com `password_hash()` (bcrypt); hÃ¡ controle de acesso por sessÃ£o; **nÃ£o hÃ¡** criptografia de dados sensÃ­veis em repouso, mascaramento de CPF nem segregaÃ§Ã£o de credenciais (senha do banco de produÃ§Ã£o hardcoded no `config.php` e segredos no `.env` local).

---

## Linguagens e Frameworks

| Item | Status |
|---|---|
| Linguagem(ns) de programaÃ§Ã£o backend utilizado(s) | X |
| Framework backend utilizado | â˜ |
| Linguagem e framework frontend utilizados | X |
| Frameworks de estilo/UI utilizados | X |
| Linguagem para scripts e automaÃ§Ãµes | X |
| Bibliotecas de terceiros e dependÃªncias (gerenciador de pacotes) | X |

ObservaÃ§Ãµes: ____________________________________________________________

- **Backend:** **PHP 8.3+** com **PDO** (camada de acesso a dados) â€” `backEnd/config/config.php` (classe `Conexao`, singleton PDO), `backEnd/models/*DAO.php`, `backEnd/controllers/api/*`.
- **Framework backend:** **nenhum** â€” PHP puro com MVC artesanal (decisÃ£o de projeto; sem Laravel/Slim).
- **Frontend:** **HTML5 + CSS3 + JavaScript Vanilla** â€” `frontEnd/views/*.html` (31 pÃ¡ginas), `frontEnd/utils/*.js` (31 scripts: `app.js`, `chat.js`, `pagamento.js`, `animalValidacao.js`, `formatacao.js` etc.); sem framework JS (React/Vue).
- **Estilo/UI:** **CSS puro** com variÃ¡veis customizadas e paleta verde â€” `frontEnd/style/style.css`, `estilos.css`, `chat.css`; sem Bootstrap/Tailwind.
- **Scripts/automaÃ§Ã£o:** JavaScript no cliente (validaÃ§Ãµes de formulÃ¡rio e consumo da API via `fetch`), PHP no servidor; testes automatizados via CLI do PHPUnit.
- **DependÃªncias:** **Composer** (`composer.json` + `composer.lock`) â€” **PHPMailer ^7.1** (envio de e-mail SMTP) e **PHPUnit 11** (desenvolvimento, `phpunit.xml`); integraÃ§Ã£o externa com a API do **AbacatePay** (pagamentos).

---

## Arquitetura e Infraestrutura

| Item | Status |
|---|---|
| Ambiente de hospedagem identificado (on-premise ou cloud) | X |
| Servidor web utilizado | X |
| ContainerizaÃ§Ã£o e orquestraÃ§Ã£o (se aplicÃ¡vel) | â˜ |
| EstratÃ©gia de escalabilidade identificada | â˜ |
| IntegraÃ§Ã£o via APIs e protocolos utilizados | X |

ObservaÃ§Ãµes: ____________________________________________________________

- **Hospedagem:** desenvolvimento local com **XAMPP**; produÃ§Ã£o em **servidor compartilhado remoto** (host MySQL `tini.click`, banco `pet_alliance_db`) â€” ambiente on-premise/cloud compartilhado, nÃ£o containerizado.
- **Servidor web:** **Apache com mÃ³dulo mod_rewrite** (rotas amigÃ¡veis); gerenciamento do banco via phpMyAdmin.
- **ContainerizaÃ§Ã£o:** **nÃ£o hÃ¡** Docker/docker-compose â€” deploy direto por upload de arquivos (FTP) para o servidor compartilhado.
- **Escalabilidade:** **sem estratÃ©gia formal** â€” a paginaÃ§Ã£o existe na API (LIMIT/OFFSET em `animalDAO.php` â€” `search()` e `readAll()`), mas sem cache, sem lazy loading e sem balanceamento de carga; apenas os requisitos RNF02 (suporte a 100+ usuÃ¡rios simultÃ¢neos) e RNF05 (disponibilidade 24/7) documentados. PendÃªncia de melhoria.
- **IntegraÃ§Ãµes/APIs:** API REST prÃ³pria (controllers em `backEnd/controllers/api/` retornando JSON, consumida via `fetch` no frontend); **AbacatePay** (API REST para checkout + webhook em `abacatepay-webhook.php`); **ViaCEP** (consulta de CEP); **SMTP** via PHPMailer (Gmail) para e-mails transacionais. Protocolos: HTTP/HTTPS, JSON, SMTP.

---

## SeguranÃ§a

| Item | Status |
|---|---|
| Mecanismo de autenticaÃ§Ã£o identificado | X |
| Controle de autorizaÃ§Ã£o e nÃ­veis de permissÃ£o | X |
| ProteÃ§Ãµes contra vulnerabilidades comuns verificadas | X |
| Certificados SSL/TLS e criptografia em trÃ¢nsito | â˜ |
| Logs de auditoria e monitoramento de acessos | â˜ |

ObservaÃ§Ãµes: ____________________________________________________________

- **AutenticaÃ§Ã£o:** **sessions PHP** com `session_regenerate_id(true)` no login (`backEnd/usuario/loginUsuario.php`); senhas com `password_hash()`/`password_verify()` (bcrypt); **verificaÃ§Ã£o de e-mail obrigatÃ³ria** antes do primeiro login (token `bin2hex(random_bytes(32))` + cÃ³digo de 6 dÃ­gitos, expiraÃ§Ã£o de 30 min); campos de controle de bloqueio por tentativas (`tentativas_login`, `bloqueado` na tabela `tb_usuarios`).
- **AutorizaÃ§Ã£o:** nÃ­veis por `tipo_usuario` (0 = comum, 1 = admin) com checagem no painel administrativo (`backEnd/admin/*` â€” acesso negado redireciona para home); ediÃ§Ã£o de pet restrita ao dono (RF03.5 â€” `UPDATE ... AND dono_id=?`). **Ressalva:** a exclusÃ£o de pet nÃ£o verifica dono/admin (falha IDOR â€” ver item 5 da seÃ§Ã£o de problemas).
- **ProteÃ§Ãµes verificadas:** **PDO prepared statements** em todas as consultas (anti SQL Injection); validaÃ§Ã£o de MIME real via `finfo` + `.htaccess` bloqueando execuÃ§Ã£o de PHP na pasta `uploads/`; sanitizaÃ§Ã£o de CPF/CEP (`preg_replace`); `FILTER_VALIDATE_EMAIL`; validaÃ§Ã£o de senha forte na redefiniÃ§Ã£o. PendÃªncias: sem tokens **CSRF** e sem escape sistemÃ¡tico de saÃ­da (risco residual de XSS).
- **SSL/TLS:** nÃ£o configurado no cÃ³digo (URLs relativas, formulÃ¡rios enviam senha em texto plano); o HTTPS fica a cargo do servidor de hospedagem â€” recomenda-se forÃ§ar HTTPS em produÃ§Ã£o.
- **Logs/auditoria:** apenas `error_log` nativo do PHP; **sem** trilha de auditoria de acessos e operaÃ§Ãµes sensÃ­veis (ex.: logins, pagamentos, aÃ§Ãµes de admin).

---

## Testes e Qualidade

| Item | Status |
|---|---|
| Frameworks de teste unitÃ¡rio identificados | X |
| Testes de integraÃ§Ã£o e end-to-end identificados | X |
| Cobertura de cÃ³digo e relatÃ³rios de qualidade | X |

ObservaÃ§Ãµes: ____________________________________________________________

- **Testes unitÃ¡rios:** **PHPUnit 11** â€” `phpunit.xml` (bootstrap `tests/bootstrap.php`, suite `Unit`), 4 suÃ­tes em `tests/Unit/` (`usuarioDAOTest`, `animalDAOTest`, `denunciaDAOTest`, `solicitacaoMatchDAOTest`) com mocks de `PDO`/`PDOStatement`.
- **IntegraÃ§Ã£o/E2E:** planos e roteiros manuais em `docs/plano-de-testes.md` (funcional E2E, ciclos por RF), `docs/roteiro-testes.md` (RT01â€“RT08), `docs/teste-de-mesa.md` (TM01â€“TM04) e `docs/cenarios-bdd.md` (Gherkin); testes de API com Postman/Insomnia (README); sem automaÃ§Ã£o E2E (Selenium/Playwright).
- **Cobertura/qualidade:** `<source>` configurado no `phpunit.xml` (cobertura de `backEnd/models` e `backEnd/controllers`); `docs/diagnostico-iso25010.md` (avaliaÃ§Ã£o ISO/IEC 25010 â€” adequaÃ§Ã£o funcional 3,7; eficiÃªncia 3,3; compatibilidade 4,0); `docs/checklist-acessibilidade.md`; `docs/relatorio-status.md` (modelo CSR). PendÃªncias: relatÃ³rio de cobertura em HTML nÃ£o gerado e **sem CI/CD** â€” execuÃ§Ã£o apenas local; os 48 CTs da RTM permanecem com status "Pendente".

---

## DocumentaÃ§Ã£o e GovernanÃ§a

| Item | Status |
|---|---|
| DocumentaÃ§Ã£o tÃ©cnica disponÃ­vel | X |
| Controle de versÃ£o utilizado e estratÃ©gia de branching | X |
| ConvenÃ§Ãµes de nomenclatura e padrÃµes de codificaÃ§Ã£o | X |

ObservaÃ§Ãµes: ____________________________________________________________

- **DocumentaÃ§Ã£o tÃ©cnica:** `README.md` (visÃ£o geral, tecnologias, instalaÃ§Ã£o, estrutura de diretÃ³rios, planos) + pasta `docs/` com 16 documentos: SRS, user stories, cenÃ¡rios BDD, matriz de rastreabilidade, plano de testes, roteiro de testes, teste de mesa, bug reports, escopo negativo, diagnÃ³stico ISO 25010, checklist de acessibilidade, plano de remediaÃ§Ã£o, anÃ¡lise documental, sessÃ£o exploratÃ³ria, critÃ©rios e **DER em PDF**.
- **Controle de versÃ£o:** **Git + GitHub** (repositÃ³rio `github.com/ThurStelzner/PetAlliance`); branches por desenvolvedor (`main`, `carlos-branch`, `Arthur`, `alexandre`, `Teste-final`) com merges e commits descritivos (`fix:`, `feat:`). PendÃªncia: sem estratÃ©gia formal de branching (ex.: Git Flow) nem tags de release.
- **ConvenÃ§Ãµes:** tabelas com prefixo `tb_`; classes de modelo + DAO (sufixo `DAO`); controllers organizados em `backEnd/controllers/api/`; cÃ³digo em portuguÃªs; pastas por mÃ³dulo (`usuario/`, `animal/`, `denuncias/`, `admin/`). PendÃªncia: PSR-4/PSR-12 nÃ£o adotado; `composer.json` sem autoload.

---

## Descreva os problemas e erros encontrados no Desenvolvimento e Testes do Sistema:

1. **RF01.3 (MÃ©dia) â€” senha com mÃ­nimo de 8 caracteres nÃ£o validada no login.** O formulÃ¡rio usa `minlength="3"` no HTML, sem validaÃ§Ã£o no backend (`frontEnd/views/login.html`, `backEnd/usuario/loginUsuario.php`). RecomendaÃ§Ã£o: validar no backend com mensagem clara.

2. **RF02.3 (MÃ©dia) â€” cadastro aceita qualquer senha.** A funÃ§Ã£o `validarSenhaForte()` (maiÃºscula + minÃºscula + nÃºmero) sÃ³ Ã© aplicada na redefiniÃ§Ã£o de senha (`backEnd/config/validacao.php`, `backEnd/usuario/cadastrarUsuario.php`). RecomendaÃ§Ã£o: aplicar a validaÃ§Ã£o tambÃ©m no cadastro.

3. **RF01 (Baixa) â€” login aceita apenas CPF.** O SRS prevÃª login por Email/Username, CPF ou Senha; a implementaÃ§Ã£o busca somente por CPF (`frontEnd/views/login.html`, `backEnd/usuario/loginUsuario.php`). RecomendaÃ§Ã£o: aceitar CPF ou e-mail.

4. **RF11.5 (MÃ©dia) â€” limite de upload divergente.** O SRS define fotos com mÃ¡ximo de 5 MB, mas `MAX_FILE_SIZE` estÃ¡ definido como **100 MB** em `backEnd/config/config.php:27`. RecomendaÃ§Ã£o: alinhar a constante a 5 MB ou revisar formalmente o requisito.

5. **SeguranÃ§a (CrÃ­tica) â€” IDOR na exclusÃ£o de pets (autorizaÃ§Ã£o quebrada).** A rota `DELETE /backEnd/home.php?route=excluir_animal&id=X` chama `AnimalController::deletarAnimal($id)` â†’ `AnimalDAO::delete($id)` â†’ `DELETE FROM tb_pets WHERE id = ?` **sem verificar se o usuÃ¡rio logado Ã© o dono do pet ou admin** (`backEnd/home.php:172-177`, `backEnd/controllers/api/animalController.php:57-64`, `backEnd/models/animalDAO.php:454-497`). Qualquer usuÃ¡rio autenticado pode excluir o pet de terceiros informando o ID (as fotos fÃ­sicas tambÃ©m sÃ£o apagadas). O `UPDATE` de ediÃ§Ã£o verifica `dono_id`, mas o `DELETE` nÃ£o. RecomendaÃ§Ã£o (urgente): checar `dono_id` da sessÃ£o ou `tipo_usuario == 1` antes do DELETE (ex.: `DELETE FROM tb_pets WHERE id = ? AND dono_id = ?`).

6. **SeguranÃ§a (Alta) â€” credenciais de produÃ§Ã£o expostas no cÃ³digo.** A senha do banco MySQL de produÃ§Ã£o (host `tini.click`) estÃ¡ hardcoded em `backEnd/config/config.php:44-46`, e o `.env` local contÃ©m a API key real do AbacatePay e a senha de app do Gmail (SMTP). Apesar de `.gitignore` excluir `.env` e `config.php` do repositÃ³rio, recomenda-se mover todos os segredos para variÃ¡veis de ambiente no servidor e **rotacionar as chaves**.

7. **SeguranÃ§a (MÃ©dia) â€” ausÃªncia de CSRF e escape de saÃ­da.** FormulÃ¡rios sem tokens CSRF e saÃ­das sem `htmlspecialchars()` sistemÃ¡tico â€” risco residual de XSS e de requisiÃ§Ãµes forjadas. RecomendaÃ§Ã£o: implementar token CSRF por sessÃ£o e escapar todas as saÃ­das dinÃ¢micas.

8. **SeguranÃ§a (MÃ©dia) â€” vazamento de detalhes internos.** Mensagens de exceÃ§Ã£o do banco aparecem em algumas respostas JSON (`catch (Exception $e)` â†’ `$e->getMessage()`), expondo estrutura interna. RecomendaÃ§Ã£o: logar internamente e retornar mensagens genÃ©ricas ao cliente.

9. **SeguranÃ§a (MÃ©dia) â€” senha em texto plano sem HTTPS forÃ§ado.** O formulÃ¡rio de cadastro envia a senha sem criptografia em trÃ¢nsito; nÃ£o hÃ¡ redirect forÃ§ado para HTTPS no cÃ³digo. RecomendaÃ§Ã£o: forÃ§ar TLS no servidor (redirect 301) e HSTS.

10. **Banco de dados (MÃ©dia) â€” sem transaÃ§Ãµes explÃ­citas em fluxos multi-tabela.** SolicitaÃ§Ã£o de match e pagamento/assinatura nÃ£o usam `BEGIN`/`COMMIT`/`ROLLBACK` â€” em falha parcial pode haver inconsistÃªncia de dados. RecomendaÃ§Ã£o: envolver os fluxos crÃ­ticos em transaÃ§Ãµes PDO com rollback em exceÃ§Ã£o.

11. **Banco de dados (Baixa) â€” sem migrations automatizadas.** O schema Ã© versionado manualmente via `docs/banco.sql`; sem triggers/views/procedures (toda a regra na camada PHP). RecomendaÃ§Ã£o: adotar migrations numeradas com script de aplicaÃ§Ã£o idempotente.

12. **Desempenho (MÃ©dia) â€” sem cache e sem lazy loading.** O diagnÃ³stico ISO/IEC 25010 apontou eficiÃªncia 3,3/5 (comportamento temporal nota 3): consultas sÃ­ncronas sem cache de queries/HTTP, imagens sem lazy loading e buscas sem Ã­ndices dedicados nos filtros (tipo, raÃ§a, sexo, CEP). A paginaÃ§Ã£o existe na API (LIMIT/OFFSET), mas com volume real de dados o tempo de resposta tende a degradar (meta RNF02: < 3s). RecomendaÃ§Ã£o: cache (ex.: OPcache/Redis), Ã­ndices compostos e lazy loading de imagens.

13. **Qualidade de cÃ³digo (Baixa) â€” problemas de encoding (UTF-8).** Alguns arquivos apresentam acentuaÃ§Ã£o corrompida (ex.: `chatController.php` â€” "Conversa nï¿½o informada"; `validacao.php` â€” "maiÇ§scula"), indicando falta de padronizaÃ§Ã£o UTF-8 na ediÃ§Ã£o dos fontes. RecomendaÃ§Ã£o: salvar todos os fontes em UTF-8 sem BOM.

14. **Qualidade de testes (MÃ©dia) â€” testes unitÃ¡rios acoplados.** Os mocks de `PDO` sÃ£o injetados via Reflection (`ReflectionClass`) na classe concreta `Conexao` â€” ideal seria injeÃ§Ã£o de dependÃªncia por construtor (ISO 25010: testabilidade nota 2). RecomendaÃ§Ã£o: refatorar DAOs para receber PDO por construtor.

15. **Processo de testes (MÃ©dia) â€” subutilizaÃ§Ã£o dos artefatos.** RelatÃ³rio de cobertura nÃ£o Ã© gerado, nÃ£o hÃ¡ CI/CD (execuÃ§Ã£o apenas local), o `relatorio-status.md` (CSR) permanece com campos em branco e o `bug-reports.md` registra 0 defeitos funcionais (os 4 gaps SRS Ã— cÃ³digo sÃ£o listados apenas como divergÃªncias). RecomendaÃ§Ã£o: pipeline GitHub Actions com PHPUnit + cobertura e preenchimento do CSR por ciclo.

16. **Qualidade de cÃ³digo (Baixa) â€” sem comentÃ¡rios e sem padrÃ£o PSR.** Nenhum comentÃ¡rio em controllers/models/views; `composer.json` sem autoload PSR-4. RecomendaÃ§Ã£o: padronizar PSR-12, documentar contratos e configurar autoload.

17. **Infraestrutura (MÃ©dia) â€” `.htaccess` desatualizado.** As pastas de upload usam a diretiva `Deny from all` (sintaxe Apache 2.2); em Apache 2.4+ o correto Ã© `Require all denied` (`uploads/animais/.htaccess`, `uploads/usuario/.htaccess`). RecomendaÃ§Ã£o: atualizar para a sintaxe do Apache 2.4.

18. **Acessibilidade (Alta) â€” falhas crÃ­ticas de WCAG 2.1.** Taxa de aprovaÃ§Ã£o de apenas **20% (5/25 critÃ©rios)** â€” 12 falhas: carrossel de fotos inoperÃ¡vel por teclado/touch (sÃ³ `mouseenter`, `app.js:111`), labels sem `for`/`id`, mensagens flash sem `aria-live`, foco invisÃ­vel (sem `outline`), sem link "pular para conteÃºdo" e `lang` ausente em pÃ¡ginas avulsas. RecomendaÃ§Ã£o: executar as remediaÃ§Ãµes R01â€“R04 de `docs/plano-remediacao.md`.

19. **UX (MÃ©dia) â€” falhas de interface.** Placeholders "NavBar aqui" e "Footer aqui" visÃ­veis ao usuÃ¡rio (`navBar.html`, `footer.html`); filtros disparam busca automaticamente sem botÃ£o "Aplicar" (`app.js:69`); aÃ§Ãµes destrutivas usam `confirm()` nativo (`app.js:428`); mensagens de erro sem destaque visual; carrossel inoperante em mobile. RecomendaÃ§Ã£o: remover placeholders, adicionar botÃ£o "Aplicar filtros" e modal de confirmaÃ§Ã£o (R07/R10 do plano).

20. **Confiabilidade (MÃ©dia) â€” tratamento de erros genÃ©rico.** `catch (Exception $e)` genÃ©ricos sem fallback para APIs externas (AbacatePay offline quebra o fluxo de pagamento) e erros 500 sem feedback amigÃ¡vel ao usuÃ¡rio. RecomendaÃ§Ã£o: retry/circuit breaker para APIs externas e mensagens amigÃ¡veis com log interno.

---

*Checklist elaborado a partir da anÃ¡lise do cÃ³digo-fonte, documentaÃ§Ã£o (`docs/`) e repositÃ³rio GitHub (ThurStelzner/PetAlliance) em 04/08/2026.*
