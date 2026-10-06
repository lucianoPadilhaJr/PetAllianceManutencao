# Roteiro de Testes (Manual)

## 1. Objetivo

Roteiro passo a passo para execução manual dos testes funcionais do Pet Alliance. Formato resumido: fluxo principal, sem dados específicos.

---

## 2. Convenções

- **PRÉ:** pré-condição necessária
- **DADOS:** dados de entrada sugeridos
- **↓** significa "passo seguinte"
- **[✔]** = resultado esperado

---

## 3. Roteiros

---

### RT01 — Login

| ID | RT01 |
|---|---|
| **Funcionalidade** | Login |
| **PRÉ** | Usuário cadastrado e verificado |

| Passo | Ação | Resultado Esperado |
|---|---|---|
| 1 | Acessar `/index.php` | Página de login é exibida |
| 2 | Informar CPF/email **válido** e senha **válida** | Campos preenchidos |
| 3 | Clicar em "Entrar" | [✔] Redirecionado para `/backEnd/home.php` |
| 4 | Verificar navbar | [✔] Nome do usuário aparece no cabeçalho |

| Passo | Ação (Falha) | Resultado Esperado |
|---|---|---|
| 1 | Informar CPF inválido + senha qualquer | [✔] Mensagem "Usuário não encontrado" |
| 2 | Informar CPF válido + senha errada | [✔] Mensagem "Senha incorreta" |

---

### RT02 — Cadastro de Usuário

| ID | RT02 |
|---|---|
| **Funcionalidade** | Cadastro |
| **PRÉ** | Navegar para `/backEnd/usuario/cadastrarUsuario.php` |

| Passo | Ação | Resultado Esperado |
|---|---|---|
| 1 | Preencher nome, username, email, CPF, CEP, senha, confirmar senha | Campos preenchidos |
| 2 | Clicar em "Cadastrar" | [✔] Mensagem de sucesso |
| 3 | Verificar email informado | [✔] Email de verificação recebido |
| 4 | Clicar no link do email | [✔] Conta verificada |

| Variações | Ação | Resultado Esperado |
|---|---|---|
| CPF duplicado | Cadastrar com CPF já existente | [✔] "CPF já cadastrado" |
| Email duplicado | Cadastrar com email já existente | [✔] "Email já cadastrado" |
| Senha sem validação | Qualquer senha é aceita | [✔] Cadastro concluído sem validação de complexidade |

---

### RT03 — Cadastro de Pet

| ID | RT03 |
|---|---|
| **Funcionalidade** | Cadastro de Pet |
| **PRÉ** | Usuário logado. Acessar `/backEnd/animal/cadastrarAnimal.php` |

| Passo | Ação | Resultado Esperado |
|---|---|---|
| 1 | Preencher nome, raça, cor, sexo, tipo, porte, idade, peso, descrição | Todos os campos preenchidos |
| 2 | Upload de 1 foto (mínimo obrigatório) | Foto aparece no preview |
| 3 | Marcar "Vacinado" | Check ativo |
| 4 | Clicar em "Cadastrar" | [✔] "Animal cadastrado com sucesso" |
| 5 | Ir para listagem | [✔] Novo pet aparece na lista |

| Variações | Ação | Resultado Esperado |
|---|---|---|
| Sem foto | Pular upload | [✔] "Foto é obrigatória" |
| 11+ fotos | Tentar upload de 11 arquivos | [✔] Apenas 10 aceitos |
| Editar pet alheio | URL manual com id de outro usuário | [✔] "Você não tem permissão" |

---

### RT04 — Listagem e Busca

| ID | RT04 |
|---|---|
| **Funcionalidade** | Listagem de Pets |
| **PRÉ** | Usuário logado. Pets de outros usuários cadastrados |

| Passo | Ação | Resultado Esperado |
|---|---|---|
| 1 | Acessar home | [✔] Cards de animais aparecem |
| 2 | Clicar em filtro "Tipo → Cachorro" | [✔] Apenas cachorros |
| 3 | Digitar nome no campo de busca | [✔] Resultados filtrados por nome |
| 4 | Buscar nome inexistente "ZZZZ" | [✔] "Nenhum animal encontrado" |

---

### RT05 — Match

| ID | RT05 |
|---|---|
| **Funcionalidade** | Match |
| **PRÉ** | Dois usuários logados (A e B), cada um com ao menos 1 pet |

| Passo | Ação | Resultado Esperado |
|---|---|---|
| 1 | (Usuário A) Clicar em "Enviar Match" no pet do B | [✔] "Solicitação enviada!" |
| 2 | (Usuário B) Ver notificações | [✔] Notificação de solicitação |
| 3 | (Usuário B) Aceitar solicitação | [✔] Match ativado |
| 4 | (Usuário A) Verificar se chat está disponível | [✔] Chat liberado |

| Variações | Ação | Resultado Esperado |
|---|---|---|
| Sem pet | Match sem ter pet cadastrado | [✔] "Cadastre um pet" |
| Recusar | B recusa solicitação | [✔] A notificado |
| Desfazer | Remover match existente | [✔] Chat desativado |
| Bloquear | Bloquear outro usuário | [✔] Não pode mais interagir |

---

### RT06 — Chat

| ID | RT06 |
|---|---|
| **Funcionalidade** | Chat |
| **PRÉ** | Match ativo entre A e B |

| Passo | Ação | Resultado Esperado |
|---|---|---|
| 1 | (A) Acessar `/backEnd/chat.php` | [✔] Lista de conversas carregada |
| 2 | (A) Clicar na conversa com B | [✔] Histórico de mensagens exibido |
| 3 | (A) Digitar "Olá!" e enviar | [✔] Mensagem aparece no chat |
| 4 | (B) Abrir chat | [✔] Mensagem de A visível com timestamp |

| Variação | Ação | Resultado Esperado |
|---|---|---|
| Sem match | Tentar acessar chat com id de solicitação não aceita | [✔] Bloqueado |
| Excluir | Clicar em "Excluir conversa" | [✔] Conversa removida |

---

### RT07 — Venda

| ID | RT07 |
|---|---|
| **Funcionalidade** | Venda de Filhotes |
| **PRÉ** | Usuário logado, dono de ao menos 1 pet |

| Passo | Ação | Resultado Esperado |
|---|---|---|
| 1 | Editar pet e preencher "Preço de venda = 2500" | Campo salvo |
| 2 | Salvar alterações | [✔] Preço exibido no card do pet |
| 3 | Editar novamente e alterar preço | [✔] Preço atualizado |
| 4 | Remover preço (deixar vazio) | [✔] Anúncio removido |

---

### RT08 — Pagamentos

| ID | RT08 |
|---|---|
| **Funcionalidade** | Pagamentos |
| **PRÉ** | Usuário logado. API AbacatePay configurada no .env |

| Passo | Ação | Resultado Esperado |
|---|---|---|
| 1 | Acessar página de planos | [✔] Planos exibidos com preços |
| 2 | Selecionar "Básico R$15" | [✔] Redirecionado ao checkout |
| 3 | Simular pagamento (sandbox) | [✔] Status "PAID" retornado |
| 4 | Voltar para plataforma | [✔] Benefício do plano ativado |
| 5 | Acessar "Histórico de pagamentos" | [✔] Transação registrada |

| Variação | Ação | Resultado Esperado |
|---|---|---|
| Cancelar | Cancelar pagamento pendente | [✔] Status "CANCELLED" |
| Webhook | Verificar se webhook atualizou BD | [✔] `tb_pagamentos.status` = PAID |

---

## 4. Histórico de Revisões

| Versão | Data | Autor | Alteração |
|---|---|---|---|
| 1.0 | 08/07/2026 | Arthur Iantas Stelzner | Criação inicial |
| 1.1 | 08/07/2026 | Arthur Iantas Stelzner | RT01: removido bloqueio de login. RT02: removido CAPTCHA. RT03: atualizado limite de 11+ fotos |
| 1.2 | 08/07/2026 | Arthur Iantas Stelzner | RT02: variação "Senha fraca" alterada — código não valida senha forte |
