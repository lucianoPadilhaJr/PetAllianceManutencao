# RelatÃ³rio de SessÃ£o ExploratÃ³ria

## 1. IdentificaÃ§Ã£o

| Campo | Valor |
|---|---|
| **SessÃ£o** | SES-001 |
| **Data** | 08/07/2026 |
| **Analista** | Arthur Iantas Stelzner |
| **DuraÃ§Ã£o** | 4h (anÃ¡lise de cÃ³digo + lÃ³gica) |
| **TÃ©cnica** | Leitura crÃ­tica do cÃ³digo-fonte + heurÃ­sticas de erro |
| **Escopo** | Backend (PHP) + Frontend (HTML/CSS/JS) |

---

## 2. Charter (MissÃ£o)

> Explorar o cÃ³digo do Pet Alliance em busca de falhas de seguranÃ§a, validaÃ§Ã£o, UX e lÃ³gica de negÃ³cio â€” sem roteiro prÃ©-definido, usando intuiÃ§Ã£o e heurÃ­sticas.

---

## 3. HeurÃ­sticas Utilizadas

- **H1:** Visibilidade do status do sistema
- **H2:** CorrespondÃªncia entre sistema e mundo real
- **H3:** Controle e liberdade do usuÃ¡rio
- **H4:** ConsistÃªncia e padronizaÃ§Ã£o
- **H5:** PrevenÃ§Ã£o de erros
- **H6:** Reconhecimento em vez de memorizaÃ§Ã£o
- **H7:** EficiÃªncia e flexibilidade
- **H8:** EstÃ©tica e design minimalista
- **H9:** Ajude o usuÃ¡rio a reconhecer, diagnosticar e recuperar-se de erros
- **H10:** Ajuda e documentaÃ§Ã£o

---

## 4. Achados

### 4.1. ObservaÃ§Ãµes sobre ValidaÃ§Ã£o

> **Nota:** O sistema nÃ£o implementa validaÃ§Ã£o de senha forte (maiÃºscula + minÃºscula + nÃºmero) nem mÃ­nimo de 8 caracteres. Login aceita apenas CPF como identificador. Essas sÃ£o caracterÃ­sticas atuais da implementaÃ§Ã£o, nÃ£o bugs funcionais. Ver seÃ§Ã£o de divergÃªncias no documento `bug-reports.md`.

### 4.2. Falhas de UX

| # | HeurÃ­stica | Achado | Arquivo | Gravidade |
|---|---|---|---|---|
| F05 | H1 (Visibilidade) | **Mensagens de erro sem destaque visual** â€” apenas texto vermelho solto, sem Ã­cone ou container padronizado | `loginUsuario.php:18` | ðŸŸ¡ MÃ©dia |
| F06 | H3 (Controle) | **Filtros disparam busca automaticamente** ao marcar checkbox, sem botÃ£o "Aplicar" | `frontEnd/utils/app.js:69` | ðŸŸ¢ Baixa |
| F07 | H7 (EficiÃªncia) | **Carrossel de fotos apenas no hover (mouseenter)** â€” inoperÃ¡vel em mobile touch e teclado | `frontEnd/utils/app.js:111` | ðŸ”´ Alta |
| F08 | H8 (EstÃ©tica) | **NavBar e Footer com placeholders** â€” "NavBar aqui" e "Footer aqui" visÃ­veis ao usuÃ¡rio | `frontEnd/views/navBar.html:11`, `footer.html:2` | ðŸŸ¢ Baixa |
| F09 | H3 (Controle) | **AÃ§Ãµes destrutivas usam `confirm()` nativo** â€” sem modal estilizado com descriÃ§Ã£o clara | `frontEnd/utils/app.js:428` | ðŸŸ¡ MÃ©dia |

### 4.3. Falhas de LÃ³gica de NegÃ³cio

| # | HeurÃ­stica | Achado | Arquivo | Gravidade |
|---|---|---|---|---|
| F10 | H4 (ConsistÃªncia) | **`redefinirSenha` e login usam mÃ­nimo de ~3 caracteres** â€” internamente consistentes, mas divergem do SRS (RF01.3 prevÃª 8) | `usuarioController.php:220`, `login.html:16` | ðŸŸ¢ Baixa |
| F11 | H5 (PrevenÃ§Ã£o) | **Sem verificaÃ§Ã£o de propriedade no back-end ao excluir animal** â€” apenas checa se admin ou nÃ£o, sem verificar se o usuÃ¡rio Ã© o dono | `app.js:428` + `home.php` route `excluir_animal` | ðŸ”´ Alta |
| F12 | H10 (DocumentaÃ§Ã£o) | **CÃ³digo sem comentÃ¡rios** â€” nenhum `//` ou `/** */` em controllers, models ou views PHP | Todo o backend | ðŸŸ¢ Baixa |

### 4.4. Falhas de Acessibilidade

| # | HeurÃ­stica | Achado | Arquivo | Gravidade |
|---|---|---|---|---|
| F13 | H1 (Visibilidade) | **Foco invisÃ­vel** â€” sem `outline` ou `:focus` em inputs/botÃµes | `style.css` + HTML inline | ðŸ”´ Alta |
| F14 | H6 (Reconhecimento) | **Labels sem `for`** â€” campos nÃ£o associados aos labels | `cadastrarAnimal.html`, `home.html` | ðŸ”´ Alta |
| F15 | H1 (Visibilidade) | **Mensagens flash sem `aria-live`** â€” leitores de tela nÃ£o percebem feedback | `backEnd/home.php` | ðŸŸ¡ MÃ©dia |
| F16 | H2 (Mundo real) | **Idioma nÃ£o declarado** em pÃ¡ginas avulsas (ex: configuracoes.html) | `configuracoes.html`, vÃ¡rios outros | ðŸŸ¡ MÃ©dia |

---

## 5. Mapa de Calor por MÃ³dulo

| MÃ³dulo | Achados | Gravidade MÃ©dia |
|---|---|---|
| AutenticaÃ§Ã£o (login, recovery) | F05, F10 | ðŸŸ¡ 1 alta |
| Cadastro de UsuÃ¡rio | F12 | ðŸŸ¢ baixa |
| Cadastro de Pet | F14 | ðŸŸ¡ mÃ©dia |
| Home / Listagem | F06, F07, F13, F15 | ðŸ”´ 2 altas |
| Match / Chat | Nenhum | âœ… limpo |
| Admin | F11 | ðŸ”´ 1 alta |
| ConfiguraÃ§Ãµes | F08, F16 | ðŸŸ¢ baixas |

---

## 6. Notas Livres (Achados Adicionais)

- **F11 (exclusÃ£o de animal):** O frontend usa `DELETE /backEnd/home.php?route=excluir_animal&id=X`. No backend (`home.php`), o controller `AnimalController::deletarAnimal` Ã© chamado, mas nÃ£o verifica se o `usuario_id` da sessÃ£o Ã© o dono do pet ou admin. Qualquer usuÃ¡rio logado pode deletar qualquer animal se souber o ID.
- **Senha visÃ­vel no HTML:** O formulÃ¡rio de cadastro envia senha em texto plano. Sem HTTPS, isso Ã© um risco de interceptaÃ§Ã£o.
- **Erro 500 sem tratamento:** Diversos `catch (Exception $e)` apenas logam no console do JS, sem feedback para o usuÃ¡rio.

---

## 7. MÃ©tricas da SessÃ£o

| MÃ©trica | Valor |
|---|---|
| DuraÃ§Ã£o | 4h |
| Total de achados | 12 |
| ðŸ”´ Alta gravidade | 4 |
| ðŸŸ¡ MÃ©dia gravidade | 3 |
| ðŸŸ¢ Baixa gravidade | 5 |
| Taxa de achados/hora | 3 achados/h |
| Bugs de seguranÃ§a | 0 |
| Bugs de UX | 5 |
| Bugs de lÃ³gica | 3 |
| Bugs de acessibilidade | 4 |

---

## 8. Checklist de Encaminhamento

- [ ] DivergÃªncias SRS vs. cÃ³digo registradas em `bug-reports.md`
- [x] Achados de acessibilidade registrados no Checklist de Acessibilidade
- [x] SoluÃ§Ãµes propostas no Plano de RemediaÃ§Ã£o
- [ ] Gaps de requisitos reportados aos desenvolvedores
- [ ] Reagendar sessÃ£o exploratÃ³ria para novas funcionalidades

---

## 9. HistÃ³rico de RevisÃµes

| VersÃ£o | Data | Autor | AlteraÃ§Ã£o |
|---|---|---|---|
| 1.0 | 08/07/2026 | Arthur Iantas Stelzner | SessÃ£o exploratÃ³ria inicial |
| 1.2 | 08/07/2026 | Arthur Iantas Stelzner | Removidos F01-F04 (validaÃ§Ã£o de senha/email) â€” alinhamento com cÃ³digo real; mÃ©tricas atualizadas |
