# User Stories com Critérios de Aceite

## 1. Objetivo

Documentar as funcionalidades principais do Pet Alliance no formato de **User Stories** com **Critérios de Aceite** binários (passa/falha), definindo quando cada funcionalidade está "pronta" do ponto de vista do usuário.

---

## 2. Estrutura

Cada User Story segue o formato:

> **Como** <papel> **quero** <funcionalidade> **para** <benefício>

**Critérios de Aceite** (condições testáveis):

- [ ] CA01: condição 1
- [ ] CA02: condição 2

---

## 3. User Stories

### US01 — Autenticação de Usuário

| Campo | Valor |
|---|---|
| **ID** | US01 |
| **História** | Como **usuário não logado** quero **fazer login no sistema** para **acessar minha conta e usar as funcionalidades da plataforma** |
| **RF relacionado** | RF01 |
| **Prioridade** | Alta |

**Critérios de Aceite:**

- [ ] CA01-01: Login realizado com sucesso ao informar CPF/email + senha válidos e clicar em "Entrar"
- [ ] CA01-02: Sistema exibe mensagem de erro ao informar CPF/email inexistente
- [ ] CA01-03: Sistema exibe mensagem de erro ao informar senha incorreta
- [ ] CA01-04: Sistema aceita senha sem validação de tamanho mínimo no login (minlength="3" no HTML)
- [ ] CA01-05: Usuário consegue solicitar recuperação de senha informando o email cadastrado
- [ ] CA01-06: Usuário recebe email com código/link para redefinir a senha
- [ ] CA01-07: Usuário consegue redefinir a senha com sucesso usando o link/código recebido

---

### US02 — Cadastro de Usuário

| Campo | Valor |
|---|---|
| **ID** | US02 |
| **História** | Como **novo usuário** quero **me cadastrar na plataforma** para **criar minha conta e cadastrar meus animais** |
| **RF relacionado** | RF02 |
| **Prioridade** | Alta |

**Critérios de Aceite:**

- [ ] CA02-01: Cadastro concluído com sucesso ao preencher todos os campos obrigatórios válidos
- [ ] CA02-02: Sistema rejeita cadastro com CPF já existente no banco
- [ ] CA02-03: Sistema rejeita cadastro com email já existente no banco
- [ ] CA02-04: Sistema aceita senha sem validação de complexidade (qualquer string é aceita)
- [ ] CA02-05: Sistema envia email de verificação após cadastro bem-sucedido
- [ ] CA02-06: Conta fica com status "não verificado" até que o email seja confirmado

---

### US03 — Cadastro de Pet

| Campo | Valor |
|---|---|
| **ID** | US03 |
| **História** | Como **usuário logado** quero **cadastrar meus animais na plataforma** para **encontrar matches e vender filhotes** |
| **RF relacionado** | RF03 |
| **Prioridade** | Alta |

**Critérios de Aceite:**

- [ ] CA03-01: Cadastro concluído ao preencher nome, raça, cor, sexo, tipo, porte, idade, peso, descrição e foto
- [ ] CA03-02: Sistema aceita no máximo **10 fotos** por pet
- [ ] CA03-03: Sistema exige ao menos 1 foto para concluir o cadastro
- [ ] CA03-04: Sistema exige o campo "vacinado" (carteira de vacinação)
- [ ] CA03-05: Certificado de raça é opcional (pode ser ignorado)
- [ ] CA03-06: Apenas o dono do pet pode editar os dados do animal
- [ ] CA03-07: Apenas o dono do pet pode excluir o animal

---

### US04 — Listagem e Busca de Pets

| Campo | Valor |
|---|---|
| **ID** | US04 |
| **História** | Como **usuário logado** quero **visualizar e buscar animais cadastrados** para **encontrar parceiros para meu pet** |
| **RF relacionado** | RF04 |
| **Prioridade** | Média |

**Critérios de Aceite:**

- [ ] CA04-01: Página inicial exibe lista de animais cadastrados (exceto os do próprio usuário)
- [ ] CA04-02: É possível filtrar por tipo (cachorro, gato, cavalo, etc.)
- [ ] CA04-03: É possível filtrar por raça
- [ ] CA04-04: É possível filtrar por sexo
- [ ] CA04-05: É possível buscar por nome do pet
- [ ] CA04-06: Resultados da busca exibem foto, nome, raça e localização do pet

---

### US05 — Match de Animais

| Campo | Valor |
|---|---|
| **ID** | US05 |
| **História** | Como **usuário logado com pet cadastrado** quero **enviar e receber solicitações de match** para **conectar meu animal com outros da plataforma** |
| **RF relacionado** | RF05 |
| **Prioridade** | Alta |

**Critérios de Aceite:**

- [ ] CA05-01: Usuário consegue enviar solicitação de match para um pet de outro usuário
- [ ] CA05-02: Sistema bloqueia envio de match se o usuário não tiver ao menos 1 pet cadastrado
- [ ] CA05-03: Destinatário recebe notificação da solicitação de match
- [ ] CA05-04: Destinatário pode aceitar a solicitação
- [ ] CA05-05: Destinatário pode recusar a solicitação
- [ ] CA05-06: Ao aceitar, o match é ativado e o chat é liberado
- [ ] CA05-07: Usuário pode desfazer um match existente
- [ ] CA05-08: Usuário pode bloquear outro usuário

---

### US06 — Chat

| Campo | Valor |
|---|---|
| **ID** | US06 |
| **História** | Como **usuário com match ativo** quero **conversar com o dono do outro pet** para **combinar detalhes do acasalamento ou venda** |
| **RF relacionado** | RF06 |
| **Prioridade** | Alta |

**Critérios de Aceite:**

- [ ] CA06-01: Chat fica disponível apenas após o match ser aceito por ambas as partes
- [ ] CA06-02: Usuário consegue enviar mensagens de texto
- [ ] CA06-03: Mensagens exibem data e hora do envio
- [ ] CA06-04: Mensagens são armazenadas e recuperadas ao reabrir o chat
- [ ] CA06-05: Usuário consegue excluir a conversa
- [ ] CA06-06: Sistema bloqueia tentativa de acessar chat sem match ativo

---

### US07 — Venda de Filhotes

| Campo | Valor |
|---|---|
| **ID** | US07 |
| **História** | Como **dono de um pet** quero **anunciar filhotes para venda** para **comercializar meus animais na plataforma** |
| **RF relacionado** | RF07 |
| **Prioridade** | Média |

**Critérios de Aceite:**

- [ ] CA07-01: Usuário consegue definir um preço de venda ao cadastrar ou editar um pet
- [ ] CA07-02: Apenas o dono do pet pode criar o anúncio de venda
- [ ] CA07-03: Anúncio exibe o preço na listagem de pets
- [ ] CA07-04: Dono pode editar o preço do anúncio
- [ ] CA07-05: Dono pode remover o anúncio (excluir o preço)

---

### US08 — Pagamentos

| Campo | Valor |
|---|---|
| **ID** | US08 |
| **História** | Como **usuário** quero **realizar pagamentos via plataforma** para **adquirir planos de destaque e concluir transações** |
| **RF relacionado** | RF08 |
| **Prioridade** | Alta |

**Critérios de Aceite:**

- [ ] CA08-01: Sistema cria checkout com valor correto ao solicitar pagamento
- [ ] CA08-02: Taxa do sistema é aplicada automaticamente ao valor
- [ ] CA08-03: Transação é registrada no banco de dados (tb_pagamentos)
- [ ] CA08-04: Usuário consegue visualizar histórico de pagamentos
- [ ] CA08-05: Webhook do AbacatePay atualiza o status do pagamento
- [ ] CA08-06: Usuário pode cancelar um pagamento pendente
- [ ] CA08-07: Após pagamento confirmado, os benefícios (ex: destaque) são liberados

---

## 4. Histórico de Revisões

| Versão | Data | Autor | Alteração |
|---|---|---|---|
| 1.0 | 08/07/2026 | Arthur Iantas Stelzner | Criação inicial |
| 1.1 | 08/07/2026 | Arthur Iantas Stelzner | US01: removido CA de bloqueio (RF01.5 removido). US02: removido CA de CAPTCHA (RF02.4 removido). US03: atualizado limite para 10 fotos |
| 1.2 | 08/07/2026 | Arthur Iantas Stelzner | US01 CA01-04: alterado para refletir que não há validação de 8 caracteres. US02 CA02-04: alterado para refletir que não há validação de senha forte |
