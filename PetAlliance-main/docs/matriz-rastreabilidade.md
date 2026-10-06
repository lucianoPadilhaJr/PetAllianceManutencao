# Matriz de Rastreabilidade (RTM) — Pet Alliance

## 1. Objetivo

Conectar cada requisito funcional (RF) e não funcional (RNF) do sistema **Pet Alliance** aos seus respectivos casos de teste e controle de defeitos, provando a cobertura total do sistema.

---

## 2. Legenda

| Coluna | Descrição |
|---|---|
| **ID** | Identificador do requisito (conforme SRS) |
| **Descrição** | Nome/frase resumo do requisito |
| **CT** | ID do Caso de Teste associado |
| **CT - Nome** | Nome do Caso de Teste |
| **CT - Status** | Passed / Failed / Blocked / Not Executed |
| **Defeito ID** | Identificador do defeito (se houver) |
| **Defeito - Status** | Open / Fixed / Closed / Rejected |

---

## 3. RTM — Requisitos Funcionais

### RF01 — Autenticação de Usuário

| ID | Descrição | CT | CT - Nome | CT - Status | Defeito ID | Defeito - Status |
|---|---|---|---|---|---|---|
| RF01 | Login na plataforma | CT01-01 | Login com credenciais válidas | Pendente | — | — |
| RF01.1 | Validar CPF válido | CT01-02 | Login com CPF inválido | Pendente | — | — |
| RF01.2 | Validar se o email existe | CT01-03 | Login com email inexistente | Pendente | — | — |
| RF01.3 | Senha com mínimo 8 caracteres *(não implementado)* | CT01-04 | Login — senha sem validação de mínimo | Pendente | — | — |
| RF01.4 | Recuperação de senha por email | CT01-05 | Recuperar senha via email | Pendente | — | — |

### RF02 — Cadastro de Usuário

| ID | Descrição | CT | CT - Nome | CT - Status | Defeito ID | Defeito - Status |
|---|---|---|---|---|---|---|
| RF02 | Cadastrar novo usuário | CT02-01 | Cadastro com dados válidos | Pendente | — | — |
| RF02.1 | CPF único (não duplicado) | CT02-02 | Cadastro com CPF já existente | Pendente | — | — |
| RF02.2 | Email único | CT02-03 | Cadastro com email já existente | Pendente | — | — |
| RF02.3 | Senha: Maiúscula, Minúscula, Número *(não implementado)* | CT02-04 | Cadastro — senha sem validação | Pendente | — | — |
| RF02.5 | Envio de email de confirmação | CT02-05 | Receber email de verificação | Pendente | — | — |

### RF03 — Cadastro de Pet

| ID | Descrição | CT | CT - Nome | CT - Status | Defeito ID | Defeito - Status |
|---|---|---|---|---|---|---|
| RF03 | Registrar animal | CT03-01 | Cadastro de pet com dados válidos | Pendente | — | — |
| RF03.1 | Limite de até 10 fotos | CT03-02 | Upload com mais de 10 fotos | Pendente | — | — |
| RF03.2 | Foto é obrigatória | CT03-03 | Cadastro sem foto | Pendente | — | — |
| RF03.3 | Carteira de vacinação obrigatória | CT03-04 | Cadastro sem vacinação | Pendente | — | — |
| RF03.4 | Certificado de raça (opcional) | CT03-05 | Cadastro com certificado opcional | Pendente | — | — |
| RF03.5 | Edição permitida apenas pelo dono | CT03-06 | Editar pet de outro usuário | Pendente | — | — |

### RF04 — Listagem de Pets

| ID | Descrição | CT | CT - Nome | CT - Status | Defeito ID | Defeito - Status |
|---|---|---|---|---|---|---|
| RF04 | Visualizar animais cadastrados | CT04-01 | Listar todos os pets | Pendente | — | — |
| — | Filtro por Tipo | CT04-02 | Filtrar por tipo de animal | Pendente | — | — |
| — | Filtro por Raça | CT04-03 | Filtrar por raça | Pendente | — | — |
| — | Filtro por Sexo | CT04-04 | Filtrar por sexo | Pendente | — | — |
| — | Filtro por Localização | CT04-05 | Filtrar por localização/CEP | Pendente | — | — |
| — | Busca por nome do pet | CT04-06 | Buscar pet por nome | Pendente | — | — |

### RF05 — Match de Animais

| ID | Descrição | CT | CT - Nome | CT - Status | Defeito ID | Defeito - Status |
|---|---|---|---|---|---|---|
| RF05 | Sistema de match | CT05-01 | Enviar solicitação de match | Pendente | — | — |
| RF05.1 | Necessário ao menos 1 pet cadastrado | CT05-02 | Match sem pet cadastrado | Pendente | — | — |
| RF05.2 | Match libera chat | CT05-03 | Chat disponível após match aceito | Pendente | — | — |
| RF05.3 | Opção de desfazer match | CT05-04 | Desfazer match existente | Pendente | — | — |
| RF05.4 | Opção de bloquear usuários | CT05-05 | Bloquear outro usuário | Pendente | — | — |

### RF06 — Chat

| ID | Descrição | CT | CT - Nome | CT - Status | Defeito ID | Defeito - Status |
|---|---|---|---|---|---|---|
| RF06 | Comunicação entre proprietários | CT06-01 | Enviar mensagem no chat | Pendente | — | — |
| RF06.1 | Apenas após match | CT06-02 | Tentar chat sem match | Pendente | — | — |
| RF06.2 | Mensagens com data e hora | CT06-03 | Verificar timestamp das mensagens | Pendente | — | — |
| RF06.3 | Opção de excluir conversa | CT06-04 | Excluir conversa | Pendente | — | — |

### RF07 — Venda de Filhotes

| ID | Descrição | CT | CT - Nome | CT - Status | Defeito ID | Defeito - Status |
|---|---|---|---|---|---|---|
| RF07 | Anúncio para comercialização | CT07-01 | Criar anúncio de venda | Pendente | — | — |
| RF07.1 | Vendedor deve ser o dono do pet | CT07-02 | Tentar vender pet de outro | Pendente | — | — |
| RF07.2 | Permite edição do anúncio | CT07-03 | Editar anúncio de venda | Pendente | — | — |
| RF07.3 | Permite exclusão do anúncio | CT07-04 | Excluir anúncio de venda | Pendente | — | — |

### RF08 — Pagamentos

| ID | Descrição | CT | CT - Nome | CT - Status | Defeito ID | Defeito - Status |
|---|---|---|---|---|---|---|
| RF08 | Integração financeira | CT08-01 | Criar checkout de pagamento | Pendente | — | — |
| RF08.1 | Aplicação automática de taxas | CT08-02 | Verificar taxa aplicada | Pendente | — | — |
| RF08.2 | Registro obrigatório da transação | CT08-03 | Verificar registro no banco | Pendente | — | — |
| RF08.3 | Histórico visível ao usuário | CT08-04 | Acessar histórico de pagamentos | Pendente | — | — |
| RF08.4 | Integração via API | CT08-05 | Webhook recebe notificação | Pendente | — | — |
| RF08.5 | Opção de cancelamento | CT08-06 | Cancelar pagamento | Pendente | — | — |

### RF09 — Perfil

| ID | Descrição | CT | CT - Nome | CT - Status | Defeito ID | Defeito - Status |
|---|---|---|---|---|---|---|
| RF09 | Gerenciar dados da conta | CT09-01 | Visualizar perfil | Pendente | — | — |
| RF09 | — | CT09-02 | Editar dados do perfil | Pendente | — | — |
| RF09 | — | CT09-03 | Alterar foto de perfil | Pendente | — | — |

### RF10 — Administração

| ID | Descrição | CT | CT - Nome | CT - Status | Defeito ID | Defeito - Status |
|---|---|---|---|---|---|---|
| RF10 | Painel de controle | CT10-01 | Acessar painel admin | Pendente | — | — |
| — | Remover usuários | CT10-02 | Remover usuário pelo admin | Pendente | — | — |
| — | Remover pets | CT10-03 | Remover pet pelo admin | Pendente | — | — |
| — | Remover anúncios | CT10-04 | Remover anúncio pelo admin | Pendente | — | — |
| — | Visualizar denúncias | CT10-05 | Listar denúncias no admin | Pendente | — | — |
| — | Visualizar transações | CT10-06 | Listar transações no admin | Pendente | — | — |

### RF11 — Configurações e Privacidade

| ID | Descrição | CT | CT - Nome | CT - Status | Defeito ID | Defeito - Status |
|---|---|---|---|---|---|---|
| RF11 | Ajustes da conta | CT11-01 | Acessar configurações | Pendente | — | — |
| RF11.1 | Exige login ativo | CT11-02 | Acessar config sem login | Pendente | — | — |
| RF11.2 | Alterar senha exige senha atual | CT11-03 | Alterar senha com senha atual correta | Pendente | — | — |
| RF11.2 | — | CT11-04 | Alterar senha com senha atual errada | Pendente | — | — |
| RF11.5 | Fotos JPG/PNG máx 5MB | CT11-05 | Upload de foto acima do limite | Pendente | — | — |
| RF11.7 | Perfil público/privado | CT11-06 | Alternar visibilidade do perfil | Pendente | — | — |
| RF11.9 | Exclusão de conta remove dados | CT11-07 | Excluir conta e verificar remoção | Pendente | — | — |

---

## 4. RTM — Requisitos Não Funcionais

| ID | Descrição | CT | CT - Nome | CT - Status | Defeito ID | Defeito - Status |
|---|---|---|---|---|---|---|
| RNF01 | Segurança (senhas criptografadas) | CT12-01 | Verificar hash da senha no BD | Pendente | — | — |
| RNF02 | Desempenho < 3s | CT12-02 | Tempo de resposta das páginas | Pendente | — | — |
| RNF03 | Interface intuitiva e responsiva | CT12-03 | Navegação em mobile (375px) | Pendente | — | — |
| RNF03 | — | CT12-04 | Navegação em tablet (768px) | Pendente | — | — |
| RNF03 | — | CT12-05 | Navegação em desktop (1920px) | Pendente | — | — |
| RNF04 | Compatibilidade cross-browser | CT12-06 | Chrome — fluxo completo | Pendente | — | — |
| RNF04 | — | CT12-07 | Firefox — fluxo completo | Pendente | — | — |
| RNF04 | — | CT12-08 | Edge — fluxo completo | Pendente | — | — |

---

## 5. Indicadores de Cobertura

| Métrica | Valor |
|---|---|
| Total de RF mapeados no SRS | 11 principais + 28 subitens |
| Total de RNF | 6 |
| Total de Casos de Teste (CT) mapeados | 48 |
| Cobertura de RF com ao menos 1 CT | 100% (11/11) |
| Cobertura de RNF com ao menos 1 CT | 100% (6/6) |
| CT executados | 0 / 48 (0%) |
| CT aprovados | 0 / 48 (0%) |
| Defeitos abertos | 0 |

---

## 6. Instruções de Preenchimento

1. **Antes da execução:** todos os CTs devem estar com status **"Pendente"**
2. **Durante a execução:** atualizar CT - Status para:
   - `Passed` — teste passou sem problemas
   - `Failed` — teste falhou; abrir defeito
   - `Blocked` — não foi possível executar (dependência quebrada)
3. **Defeitos:** ao marcar um CT como `Failed`, abrir um registro de defeito (ID único) e vincular nesta matriz
4. **Ao final do ciclo:** preencher os indicadores de cobertura

---

## 7. Histórico de Revisões

| Versão | Data | Autor | Alteração |
|---|---|---|---|
| 1.0 | 08/07/2026 | Arthur Iantas Stelzner | Criação inicial |
| 1.1 | 08/07/2026 | Arthur Iantas Stelzner | Removidos CTs de RF01.5 (bloqueio) e RF02.4 (CAPTCHA); atualizado RF03.1 para 10 fotos |
| 1.2 | 08/07/2026 | Arthur Iantas Stelzner | RF01.3 e RF02.3 marcados como não implementados; CT01-04 e CT02-04 atualizados |
