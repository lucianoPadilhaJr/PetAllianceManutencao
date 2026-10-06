# Plano de Testes â€” Pet Alliance

## 1. IdentificaÃ§Ã£o do Documento

| Campo | Valor |
|---|---|
| **Projeto** | Pet Alliance |
| **VersÃ£o** | 1.1 |
| **Data** | 08/07/2026 |
| **ResponsÃ¡vel** | Arthur Iantas Stelzner |
| **Aprovador** | â€” |
| **Tipo de Teste** | Funcional (End-to-End / Manual) |
| **Ambiente** | Desenvolvimento |

---

## 2. Objetivo

Este plano define a estratÃ©gia, os recursos, o cronograma e a abrangÃªncia dos testes funcionais do sistema **Pet Alliance**, uma plataforma web para conexÃ£o entre tutores de animais de raÃ§a para acasalamento e venda de filhotes.

O objetivo Ã© validar que todos os requisitos funcionais atendem ao especificado no documento SRS (`docs/requisitosFuncionais.txt`), garantindo a qualidade do sistema antes de sua evoluÃ§Ã£o para ambiente de homologaÃ§Ã£o.

---

## 3. Escopo

### 3.1. Dentro do Escopo (SerÃ¡ Testado)

- AutenticaÃ§Ã£o (login, recuperaÃ§Ã£o de senha)
- Cadastro de usuÃ¡rios com validaÃ§Ã£o de CPF e email Ãºnico
- VerificaÃ§Ã£o de email (cÃ³digo e link)
- Cadastro, ediÃ§Ã£o, listagem e exclusÃ£o de animais (pets) â€” limite de **10 fotos**
- Match entre animais (solicitaÃ§Ã£o, aceite, recusa)
- Chat entre usuÃ¡rios com match ativo
- Venda de filhotes (anÃºncio com preÃ§o)
- Pagamentos integrados (AbacatePay)
- Perfil do usuÃ¡rio (ediÃ§Ã£o, foto, exclusÃ£o)
- Favoritar animais
- DenÃºncias (cadastro e resoluÃ§Ã£o pelo admin)
- NotificaÃ§Ãµes (solicitaÃ§Ãµes de match, lidas/nÃ£o lidas)
- Painel administrativo (gerenciamento de denÃºncias, usuÃ¡rios, pets)
- ConfiguraÃ§Ãµes e privacidade (senha, perfil pÃºblico/privado)
- Membros / Destaques (planos de destaque para animais)
- Responsividade (navegadores, celulares, tablets)
- NavegaÃ§Ã£o (navbar, footer, rotas)

### 3.2. Fora do Escopo

Consulte o documento **Escopo Negativo** (`docs/escopo-negativo.md`).

---

## 4. EstratÃ©gia de Testes

| Tipo | Abordagem | Ferramenta |
|---|---|---|
| Teste Funcional (E2E) | Manual, navegando pelo sistema como usuÃ¡rio final | Navegador (Chrome/Firefox) + Inspecionar |
| Teste de API | Opcional â€” chamadas diretas aos endpoints PHP | Postman / Insomnia |
| Teste de RegressÃ£o | ReexecuÃ§Ã£o dos casos crÃ­ticos apÃ³s cada alteraÃ§Ã£o | Checklist manual |
| Teste de AceitaÃ§Ã£o | ValidaÃ§Ã£o com o cliente/PO ao final do ciclo | DemonstraÃ§Ã£o guiada |

---

## 5. Ciclos de Teste

### 5.1. Ciclo 1 â€” Funcionalidades Core (Semanas 1â€“2)

- RF01: AutenticaÃ§Ã£o
- RF02: Cadastro de UsuÃ¡rio
- RF03: Cadastro de Pet
- RF09: Perfil

### 5.2. Ciclo 2 â€” InteraÃ§Ã£o entre UsuÃ¡rios (Semanas 3â€“4)

- RF04: Listagem de Pets
- RF05: Match de Animais
- RF06: Chat
- RF11: ConfiguraÃ§Ãµes e Privacidade

### 5.3. Ciclo 3 â€” TransaÃ§Ãµes e AdministraÃ§Ã£o (Semanas 5â€“6)

- RF07: Venda de Filhotes
- RF08: Pagamentos
- RF10: AdministraÃ§Ã£o

### 5.4. Ciclo 4 â€” RegressÃ£o e Fechamento (Semana 7)

- ReexecuÃ§Ã£o de casos crÃ­ticos
- Testes de responsividade
- ValidaÃ§Ã£o final com o PO

---

## 6. Recursos

### 6.1. Equipe

| Papel | Quantidade | Responsabilidade |
|---|---|---|
| Analista de Testes | 1 | Planejamento, execuÃ§Ã£o e reporte |
| Desenvolvedor | 2 | CorreÃ§Ã£o de defeitos |
| PO / Cliente | 1 | ValidaÃ§Ã£o e aceitaÃ§Ã£o |

### 6.2. Ambiente

- **Servidor Web:** XAMPP (Apache + PHP 8.3+)
- **Banco de Dados:** MySQL 5.7+ (pet_alliance_db)
- **Navegadores:** Chrome, Firefox, Edge (Ãºltimas versÃµes)
- **Dispositivos:** Desktop, tablet (768px), mobile (375px)
- **Ferramentas:** DevTools do navegador, GitHub Issues para defeitos

---

## 7. Riscos e MitigaÃ§Ãµes

| Risco | Probabilidade | Impacto | MitigaÃ§Ã£o |
|---|---|---|---|
| Banco de dados privado sem acesso | Alta | CrÃ­tico | Solicitar acesso com antecedÃªncia; usar dump local se possÃ­vel |
| DependÃªncia de API externa (AbacatePay) | MÃ©dia | Alto | Usar ambiente sandbox / mock da API |
| Ambiente de desenvolvimento instÃ¡vel | MÃ©dia | MÃ©dio | Documentar critÃ©rios de parada no documento especÃ­fico |
| Escopo crescer sem controle | Baixa | Alto | Seguir rigorosamente o Escopo Negativo |
| Falta de equipamentos para teste mobile | MÃ©dia | Baixo | Usar DevTools modo responsivo |

---

## 8. EntregÃ¡veis

| EntregÃ¡vel | DescriÃ§Ã£o | Local |
|---|---|---|
| Plano de Testes | Este documento | `docs/plano-de-testes.md` |
| Escopo Negativo | O que **nÃ£o** serÃ¡ testado | `docs/escopo-negativo.md` |
| CritÃ©rios de Entrada/Parada/SaÃ­da | Regras do ciclo | `docs/criterios.md` |
| Matriz de Rastreabilidade (RTM) | Requisito â†’ Caso de Teste â†’ Resultado | `docs/matriz-rastreabilidade.md` |
| RelatÃ³rio de ExecuÃ§Ã£o | Resultados e estatÃ­sticas ao final | `docs/relatorio-testes.md` |

---

## 9. Cronograma

| Fase | InÃ­cio | Fim |
|---|---|---|
| Planejamento | 08/07/2026 | 10/07/2026 |
| Ciclo 1 â€” Core | 13/07/2026 | 24/07/2026 |
| Ciclo 2 â€” InteraÃ§Ã£o | 27/07/2026 | 07/08/2026 |
| Ciclo 3 â€” TransaÃ§Ãµes | 10/08/2026 | 21/08/2026 |
| Ciclo 4 â€” RegressÃ£o | 24/08/2026 | 28/08/2026 |
| Entrega Final | 29/08/2026 | 29/08/2026 |

---

## 10. AprovaÃ§Ã£o

| Nome | Papel | Data | Assinatura |
|---|---|---|---|
| â€” | Analista de Testes | â€” | â€” |
| â€” | PO / Cliente | â€” | â€” |
