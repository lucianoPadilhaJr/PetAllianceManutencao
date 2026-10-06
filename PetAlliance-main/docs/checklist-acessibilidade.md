# Checklist de Impacto de Acessibilidade

## 1. Objetivo

Identificar barreiras de acessibilidade no código do Pet Alliance com base nas diretrizes **WCAG 2.1** (Web Content Accessibility Guidelines), níveis A e AA. A análise foi feita teoricamente sobre os arquivos HTML, CSS e JS do projeto.

---

## 2. Ferramenta

Análise manual do código-fonte (sem servidor rodando). Recomenda-se complementar com:

- **Lighthouse** (Chrome DevTools) — execução futura com servidor ativo
- **WAVE** (WebAIM) — extensão de navegador
- **NVDA / VoiceOver** — leitores de tela

---

## 3. Resultados por Categoria

### 3.1. Perceptível (Perceivable)

| # | Critério WCAG | Nível | Situação | Onde | Impacto |
|---|---|---|---|---|---|
| 1.1.1 | Texto alternativo em imagens | A | ❌ Falha | `navBar.html:14` — `<img ... alt="Foto do Usuário">` — alt genérico, deveria descrever o usuário. `cadastrarAnimal.html` — previews sem `alt` | Leitores de tela não identificam o conteúdo |
| 1.1.1 | Ícones decorativos | A | ❌ Falha | Botões de galeria (`<`, `>`) sem texto alternativo | Usuários cegos não sabem que há navegação |
| 1.2.1 | Alternativa para áudio/vídeo | A | ✅ N/A | Sistema não possui mídia |
| 1.3.1 | Informação e relacionamentos | A | ❌ Falha | `home.html` — filtros usam `<div>` e `<label>` sem associação explícita via `for`/`id`. `cadastrarAnimal.html` — labels sem `for` em quase todos os campos | Leitores de tela não associam label ao input |
| 1.3.2 | Sequência significativa | A | ✅ OK | HTML semântico básico presente (header, nav, main, footer) |
| 1.4.1 | Uso de cor | A | ❌ Falha | Mensagens de erro usam apenas cor vermelha (`color: #d32f2f`) sem ícone adicional | Daltônicos podem não perceber |
| 1.4.3 | Contraste mínimo (texto) | AA | ⚠️ Parcial | `style.css` — cores definidas sem verificação de contraste. Fundo `#f5f5f5` com texto `#333` pode ter contraste insuficiente | Dificuldade de leitura |
| 1.4.4 | Redimensionar texto até 200% | AA | ⚠️ Parcial | Layout usa `rem` em alguns lugares, mas `px` em outros (ex: `home.html` inline styles) | Texto pode quebrar ao ampliar |
| 1.4.5 | Imagens de texto | AA | ✅ OK | Não há imagens com texto |

---

### 3.2. Operável (Operable)

| # | Critério WCAG | Nível | Situação | Onde | Impacto |
|---|---|---|---|---|---|
| 2.1.1 | Teclado | A | ❌ Falha | Carrossel de fotos depende de `mouseenter`/`mouseleave` para navegação — inoperável por teclado | Usuários que não usam mouse não acessam fotos |
| 2.1.2 | Sem armadilha de teclado | A | ✅ OK | `Dialog` com `commandFor` + `command` pode ser fechado |
| 2.2.1 | Tempo ajustável | A | ℹ️ N/A | Sem timeouts críticos |
| 2.4.1 | Pular blocos | A | ❌ Falha | Sem link "Pular para conteúdo principal" | Usuários de leitor de tela precisam navegar por toda navbar |
| 2.4.2 | Título da página | A | ✅ OK | `<title>PetAlliance</title>` presente |
| 2.4.3 | Ordem do foco | A | ⚠️ Parcial | Ordem tab segue DOM, mas sem `tabindex` explícito |
| 2.4.4 | Propósito do link (contexto) | A | ❌ Falha | Links genéricos como "Clique aqui", "Voltar" sem descrição clara |
| 2.4.6 | Cabeçalhos e descrições | AA | ⚠️ Parcial | `h1` presente, mas hierarquia de headings inconsistente |
| 2.4.7 | Foco visível | AA | ❌ Falha | CSS não define `:focus` ou `outline` em inputs e botões | Usuários de teclado não veem onde estão |

---

### 3.3. Compreensível (Understandable)

| # | Critério WCAG | Nível | Situação | Onde | Impacto |
|---|---|---|---|---|---|
| 3.1.1 | Idioma da página | A | ⚠️ Parcial | `navBar.html` tem `lang="pt-BR"`, mas páginas standalone como `configuracoes.html` e `cadastrarUsuario.html` **não têm** `<html lang="pt-BR">` | Leitor de tela pode usar idioma errado |
| 3.2.1 | Foco | A | ✅ OK | Nenhuma mudança de contexto no foco |
| 3.2.2 | Entrada | A | ❌ Falha | Filtros disparam busca automaticamente ao marcar checkbox (`app.js:69`) sem aviso | Usuário pode se surpreender |
| 3.3.1 | Identificação de erros | A | ✅ OK | Mensagens de erro retornadas pelo backend |
| 3.3.2 | Rótulos ou instruções | A | ❌ Falha | Formulários têm labels visuais, mas sem associação `for`/`id` na maioria dos campos |
| 3.3.3 | Sugestão de erros | AA | ❌ Falha | "Senha incorreta" não sugere o que fazer (ex: "Verifique CAPS LOCK") |
| 3.3.4 | Prevenção de erros (legal/financeiro) | AA | ⚠️ Parcial | Pagamentos redirecionam para AbacatePay (confirmação externa), mas sem confirmação final antes de enviar |

---

### 3.4. Robusto (Robust)

| # | Critério WCAG | Nível | Situação | Onde | Impacto |
|---|---|---|---|---|---|
| 4.1.1 | Parsing | A | ✅ OK | HTML válido na maioria das páginas |
| 4.1.2 | Nome, função, valor | A | ❌ Falha | Elementos customizados (modal, carrossel) sem `role` ARIA adequada | Leitores de tela não interpretam corretamente |
| 4.1.3 | Mensagens de status | AA | ❌ Falha | Mensagens flash (sucesso/erro) sem `role="alert"` ou `aria-live` | Usuários cegos não percebem feedback |

---

## 4. Resumo

| Nível | Total Critérios | Passa | Falha | Parcial | N/A |
|---|---|---|---|---|---|
| A | 17 | 4 | 9 | 2 | 2 |
| AA | 8 | 1 | 3 | 4 | 0 |
| **Total** | **25** | **5** | **12** | **6** | **2** |

**Taxa de aprovação:** 20% (5/25) — nível crítico

---

## 5. Principais Barreiras Encontradas

| Prioridade | Problema | Impacto |
|---|---|---|
| 🔴 Alta | Carrossel de fotos inoperável por teclado | Usuários com deficiência motora não veem fotos |
| 🔴 Alta | Labels sem `for`/`id` | Leitores de tela não associam campos |
| 🔴 Alta | Mensagens de status sem `aria-live` | Feedback invisível para cegos |
| 🟡 Média | Foco invisível (`outline` removido ou ausente) | Navegação por teclado comprometida |
| 🟡 Média | Contraste de cores não verificado | Dificuldade de leitura |
| 🟡 Média | Idioma não declarado em páginas avulsas | Leitor de tela usa voz errada |
| 🟢 Baixa | Links genéricos | Contexto perdido para leitores de tela |

---

## 6. Histórico de Revisões

| Versão | Data | Autor | Alteração |
|---|---|---|---|
| 1.0 | 08/07/2026 | Arthur Iantas Stelzner | Análise teórica baseada no código-fonte |
