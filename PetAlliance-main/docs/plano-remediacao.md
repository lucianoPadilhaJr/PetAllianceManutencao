# Plano de RemediaÃ§Ã£o

## 1. Objetivo

Propor soluÃ§Ãµes prÃ¡ticas para as falhas de seguranÃ§a, UX, acessibilidade e funcionais identificadas durante as anÃ¡lises do Pet Alliance. Cada remediaÃ§Ã£o Ã© priorizada por impacto e esforÃ§o.

---

## 2. Legenda

| Prioridade | Prazo | CritÃ©rio |
|---|---|---|
| ðŸ”´ Alta | Imediato (1-3 dias) | Bloqueia funcionalidade ou fere requisito |
| ðŸŸ¡ MÃ©dia | Curto prazo (1 semana) | Melhoria significativa de qualidade |
| ðŸŸ¢ Baixa | MÃ©dio prazo (2-4 semanas) | Melhoria incremental |

---

## 3. RemediaÃ§Ãµes

### ðŸ”´ Alta Prioridade

#### R01 â€” Adicionar `aria-live` em mensagens de feedback

| Campo | Valor |
|---|---|
| **Problema** | Mensagens flash (sucesso/erro) sem `role="alert"` |
| **SoluÃ§Ã£o** | Adicionar `role="alert" aria-live="assertive"` ao container de flash messages |
| **Arquivos** | `backEnd/home.php` (flash message div), `match.php` |
| **EsforÃ§o** | 1h |

---

### ðŸŸ¡ MÃ©dia Prioridade

#### R02 â€” Associar labels a inputs com `for`/`id`

| Campo | Valor |
|---|---|
| **Problema** | FormulÃ¡rios com `<label>` sem atributo `for` |
| **SoluÃ§Ã£o** | Adicionar `id` Ãºnico em cada input e `for` correspondente no label |
| **Arquivos** | `frontEnd/views/cadastrarAnimal.html`, `frontEnd/views/home.html`, `frontEnd/views/login.html` |
| **EsforÃ§o** | 3h |

#### R03 â€” Tornar carrossel de fotos acessÃ­vel por teclado

| Campo | Valor |
|---|---|
| **Problema** | Carrossel depende de `mouseenter`/`mouseleave` |
| **SoluÃ§Ã£o** | Adicionar listeners de teclado (`keydown` â†’ setas esquerda/direita) e `role="region"` + `aria-label` |
| **Arquivos** | `frontEnd/utils/app.js` (funÃ§Ã£o `carregarCarrosselEventos`) |
| **EsforÃ§o** | 4h |

#### R04 â€” Adicionar foco visÃ­vel (`:focus`) em todos os elementos interativos

| Campo | Valor |
|---|---|
| **Problema** | NavegaÃ§Ã£o por teclado sem indicador visual |
| **SoluÃ§Ã£o** | Adicionar no CSS global: `*:focus { outline: 2px solid #4A90D9; outline-offset: 2px; }` |
| **Arquivos** | `frontEnd/style/style.css` |
| **EsforÃ§o** | 30min |

#### R05 â€” Adicionar suporte a email no login (RF01)

| Campo | Valor |
|---|---|
| **Problema** | Login aceita apenas CPF, nÃ£o email (gap de requisito RF01) |
| **Causa** | FormulÃ¡rio tem apenas campo `cpf` e backend busca sÃ³ por CPF |
| **SoluÃ§Ã£o** | (1) Renomear campo para aceitar CPF ou email; (2) backend detectar qual foi informado e buscar adequadamente |
| **Arquivos** | `frontEnd/views/login.html`, `backEnd/usuario/loginUsuario.php` |
| **EsforÃ§o** | 6h |
| **Nota** | Pode ser tratado como melhoria futura (baixa prioridade funcional) |

#### R06 â€” Adicionar `lang="pt-BR"` em pÃ¡ginas sem declaraÃ§Ã£o

| Campo | Valor |
|---|---|
| **Problema** | `configuracoes.html`, `cadastrarUsuario.html` e outras sem `lang` |
| **SoluÃ§Ã£o** | Adicionar `<html lang="pt-BR">` em todas as pÃ¡ginas |
| **Arquivos** | Todas as views HTML standalone |
| **EsforÃ§o** | 2h |

#### R07 â€” Adicionar confirmaÃ§Ã£o em aÃ§Ãµes destrutivas

| Campo | Valor |
|---|---|
| **Problema** | ExclusÃ£o de conta sem confirmaÃ§Ã£o visual clara |
| **SoluÃ§Ã£o** | Substituir `confirm()` por modal customizado com `role="alertdialog"` e `aria-describedby` |
| **Arquivos** | `frontEnd/utils/app.js` (funÃ§Ã£o `excluirAnimal`) |
| **EsforÃ§o** | 3h |

---

### ðŸŸ¢ Baixa Prioridade

#### R08 â€” Adicionar link "Pular para conteÃºdo principal"

| Campo | Valor |
|---|---|
| **Problema** | UsuÃ¡rios de leitor de tela precisam passar pela navbar |
| **SoluÃ§Ã£o** | Adicionar link oculto no topo: `<a href="#main-content" class="skip-link">Pular para conteÃºdo</a>` |
| **Arquivos** | `frontEnd/views/navBar.html` |
| **EsforÃ§o** | 1h |

#### R09 â€” Melhorar contraste de cores

| Campo | Valor |
|---|---|
| **Problema** | Cores definidas sem verificaÃ§Ã£o de contraste WCAG |
| **SoluÃ§Ã£o** | Auditar com ferramenta (ex: WebAIM Contrast Checker) e ajustar cores para ratio mÃ­nimo de 4.5:1 |
| **Arquivos** | `frontEnd/style/style.css` |
| **EsforÃ§o** | 3h |

#### R10 â€” Adicionar feedback visual em botÃµes (loading state)

| Campo | Valor |
|---|---|
| **Problema** | Ao clicar em "Enviar Match", nÃ£o hÃ¡ indicador de carregamento |
| **SoluÃ§Ã£o** | Desabilitar botÃ£o + exibir spinner/texto "Enviando..." enquanto a requisiÃ§Ã£o nÃ£o retorna |
| **Arquivos** | `frontEnd/utils/app.js` (event listener de match) |
| **EsforÃ§o** | 2h |

#### R11 â€” Implementar testes automatizados

| Campo | Valor |
|---|---|
| **Problema** | Zero testes automatizados (ISO 25010 â€” Testabilidade nota 2) |
| **SoluÃ§Ã£o** | Adicionar PHPUnit para testes unitÃ¡rios nos DAOs + Controllers. Jest para frontend |
| **EsforÃ§o** | 1-2 semanas |
| **Nota** | Requer setup inicial de ambiente de teste |

---

## 4. PriorizaÃ§Ã£o por EsforÃ§o vs. Impacto

```
Alto impacto â”‚ R01 R03               â”‚
              â”‚ R02 R04               â”‚ R05 R07
              â”‚                       â”‚
              â”‚ R06 R08               â”‚ R09 R10 R11
Baixo impactoâ”‚                       â”‚
              â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”´â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
```

**RecomendaÃ§Ã£o:** Executar primeiro o quadrante superior esquerdo (R01, R02, R03, R04) â€” alto impacto com baixo esforÃ§o.

---

## 5. Cronograma Sugerido

| Semana | AÃ§Ã£o |
|---|---|
| Semana 1 | R01 (aria-live + acessibilidade crÃ­tica) |
| Semana 2 | R02, R03, R04 (labels, carrossel, focus) |
| Semana 3 | R05, R06, R07 (login por email, lang, confirmaÃ§Ã£o) |
| Semana 4 | R08, R09, R10, R11 (skip-link, contraste, loading, testes) |

---

## 6. HistÃ³rico de RevisÃµes

| VersÃ£o | Data | Autor | AlteraÃ§Ã£o |
|---|---|---|---|
| 1.0 | 08/07/2026 | Arthur Iantas Stelzner | Plano baseado nos bugs e falhas identificados |
| 1.2 | 08/07/2026 | Arthur Iantas Stelzner | Removidas R01-R03 (validaÃ§Ãµes de senha) â€” alinhamento com cÃ³digo real; renumeraÃ§Ã£o R04â†’R01 a R14â†’R11 |
