# Teste de Mesa — Validação de Algoritmos

## 1. Objetivo

Registrar manualmente a execução lógica de variáveis e regras de negócio para validar algoritmos críticos do Pet Alliance **antes e durante a codificação**.

---

## 2. TM01 — Validação de CPF

**Onde se aplica:** Cadastro de Usuário (`backEnd/models/usuarioDAO.php` — método `validaCPF`)

**Regra:** O CPF brasileiro é validado pelo cálculo dos dígitos verificadores (DV). Formato: 11 dígitos.

### Algoritmo (simplificado)

```
1. Remover máscaras (., -)
2. Verificar se tem 11 dígitos
3. Verificar se não é sequência repetida (ex: 111.111.111-11)
4. Calcular 1º dígito verificador:
   - Soma = Σ (dígito[i] × (10 - i)) para i = 0..8
   - Resto = (soma × 10) % 11
   - Se resto == 10, resto = 0
   - DV1 deve ser == dígito[9]
5. Calcular 2º dígito verificador:
   - Soma = Σ (dígito[i] × (11 - i)) para i = 0..9
   - Resto = (soma × 10) % 11
   - Se resto == 10, resto = 0
   - DV2 deve ser == dígito[10]
```

### Tabela de Teste — CPF Válido: `529.982.247-25`

| Passo | Variável | Cálculo | Resultado |
|---|---|---|---|
| 1 | dígitos | `[5,2,9,9,8,2,2,4,7,2,5]` | OK — 11 dígitos |
| 2 | sequência repetida? | 5≠2 → não é repetida | OK |
| 3 | soma1 | 5×10 + 2×9 + 9×8 + 9×7 + 8×6 + 2×5 + 2×4 + 4×3 + 7×2 | = 50+18+72+63+48+10+8+12+14 = **295** |
| 4 | resto1 | (295×10) % 11 = 2950 % 11 | **2** |
| 5 | DV1 esperado | dígito[9] = **2** | **OK** ✅ |
| 6 | soma2 | 5×11 + 2×10 + 9×9 + 9×8 + 8×7 + 2×6 + 2×5 + 4×4 + 7×3 + 2×2 | = 55+20+81+72+56+12+10+16+21+4 = **347** |
| 7 | resto2 | (347×10) % 11 = 3470 % 11 | **5** |
| 8 | DV2 esperado | dígito[10] = **5** | **OK** ✅ |

**Resultado Final: CPF 529.982.247-25 → VÁLIDO** ✅

### Tabela de Teste — CPF Inválido: `123.456.789-00`

| Passo | Variável | Cálculo | Resultado |
|---|---|---|---|
| 1 | dígitos | `[1,2,3,4,5,6,7,8,9,0,0]` | OK — 11 dígitos |
| 2 | sequência repetida? | Não | OK |
| 3 | soma1 | 1×10 + 2×9 + 3×8 + 4×7 + 5×6 + 6×5 + 7×4 + 8×3 + 9×2 | = 10+18+24+28+30+30+28+24+18 = **210** |
| 4 | resto1 | (210×10) % 11 = 2100 % 11 | **10 → 0** |
| 5 | DV1 esperado | dígito[9] = **0** | **OK** ✅ |
| 6 | soma2 | 1×11 + 2×10 + 3×9 + 4×8 + 5×7 + 6×6 + 7×5 + 8×4 + 9×3 + 0×2 | = 11+20+27+32+35+36+35+32+27+0 = **255** |
| 7 | resto2 | (255×10) % 11 = 2550 % 11 | **9** |
| 8 | DV2 esperado = 9 | dígito[10] = **0** ≠ **9** | **FALHA** ❌ |

**Resultado Final: CPF 123.456.789-00 → INVÁLIDO** ✅ (corretamente rejeitado)

---

## 3. TM02 — Validação de Senha (Comportamento Real)

**Onde se aplica:** Cadastro de Usuário (`backEnd/usuario/cadastrarUsuario.php`) e Redefinição (`backEnd/controllers/api/usuarioController.php`)

**Regra:** O código atual **não implementa** validação de senha forte (maiúscula + minúscula + número). O cadastro aceita qualquer senha. A única validação existente está na redefinição de senha: mínimo 3 caracteres.

### Tabela de Teste

| Cenário | Senha | Comportamento do Código | Resultado |
|---|---|---|---|
| Cadastro — senha simples | `123` | Cadastro aceita sem validação | ✅ ACEITA |
| Cadastro — sem maiúscula | `abcdef1` | Cadastro aceita (sem validação) | ✅ ACEITA |
| Cadastro — sem número | `Abcdefgh` | Cadastro aceita (sem validação) | ✅ ACEITA |
| Cadastro — só números | `12345678` | Cadastro aceita (sem validação) | ✅ ACEITA |
| Cadastro — vazia | `""` | Pode falhar no banco (campo NOT NULL) | ⚠️ ERRO BD |
| Redefinir — ≥ 3 chars | `Abc` | `strlen >= 3` → aprovado | ✅ ACEITA |
| Redefinir — < 3 chars | `Ab` | `strlen < 3` → rejeitado | ❌ "mínimo 3 caracteres" |

---

## 4. TM03 — Fluxo de Match

**Onde se aplica:** RF05 — `backEnd/controllers/api/solicitacaoMatchController.php`

**Regra:** Match ocorre entre dois usuários donos de pets. Chat é liberado apenas quando match é aceito.

### Estados possíveis da solicitação

| Estado | Significado |
|---|---|
| `pendente` | Solicitação enviada, aguardando resposta |
| `aceito` | Match confirmado, chat liberado |
| `recusado` | Match negado |

### Tabela de Transição de Estados

```
                    ┌──────────┐
                    │ pendente │
                    └────┬─────┘
                 ┌───────┴────────┐
                 ▼                ▼
            ┌────────┐     ┌──────────┐
            │ aceito │     │ recusado │
            └───┬────┘     └──────────┘
                │
                ▼
         ┌──────────────┐
         │ chat liberado │
         └──────────────┘
```

### Matriz de Decisão

| Cenário | Usuário tem pet? | Pet alvo existe? | Já enviou match? | Match ativo? | Resultado |
|---|---|---|---|---|---|
| A → B válido | ✅ Sim | ✅ Sim | ❌ Não | ❌ Não | ✅ Solicitação criada (pendente) |
| A → B sem pet | ❌ Não | ✅ Sim | ❌ Não | ❌ Não | ❌ Bloqueado: "Cadastre um pet" |
| A → B já enviou | ✅ Sim | ✅ Sim | ✅ Sim | ❌ Não | ❌ Bloqueado: "Já enviou solicitação" |
| A → B já match | ✅ Sim | ✅ Sim | ✅ Sim | ✅ Sim | ❌ Bloqueado: "Match já existe" |
| A → B recusado | ✅ Sim | ✅ Sim | ❌ (recusado) | ❌ Não | ✅ Pode reenviar? (depende da regra de negócio) |

---

## 5. TM04 — Cálculo de Taxas e Planos

**Onde se aplica:** RF08 — `backEnd/config/planos.php` e lógica de destaque

**Regra:** O sistema oferece 4 planos de assinatura com limites de destaques e preços fixos.

### Tabela de Planos

| Produto ID (enviroment) | Nome | `max_destaques` | `preco` (R$) | Taxa calculada (10% exemplo) |
|---|---|---|---|---|
| `ABACATEPAY_PRODUCT_ID` | Iniciante | 5 | 7,50 | 0,75 |
| `ABACATEPAY_PRODUCT2_ID` | Básico | 10 | 15,00 | 1,50 |
| `ABACATEPAY_PRODUCT3_ID` | Profissional | 20 | 30,00 | 3,00 |
| `ABACATEPAY_PRODUCT4_ID` | Premium | 30 | 45,00 | 4,50 |

### Teste de Mesa — Controle de Destaques

**Regra:** Um usuário não pode destacar mais animais do que seu plano permite.

| Cenário | Plano | Limite | Pets em destaque | Tenta destacar | Resultado |
|---|---|---|---|---|---|
| Dentro do limite | Básico | 10 | 3 | Pet #4 | ✅ Destaque ativado |
| Atingiu o limite | Básico | 10 | 10 | Pet #11 | ❌ "Limite de destaques atingido" |
| Sem plano (padrão) | — | 0 | 0 | Pet #1 | ❌ "Adquira um plano primeiro" |
| Profissional | Profissional | 20 | 18 | Pet #19 | ✅ Destaque ativado |
| Remover destaque | Profissional | 20 | 18 | Remove pet #5 | Destagues = 17 |

### Teste de Mesa — Cálculo de Transação (Venda)

**Regra:** A taxa da plataforma é uma porcentagem sobre o valor da venda.

```
Taxa = ValorVenda × PercentualTaxa
ValorLiquidoVendedor = ValorVenda - Taxa
```

| Valor Venda (R$) | Taxa (%) | Taxa (R$) | Valor Líquido (R$) |
|---|---|---|---|
| 1.000,00 | 5% | 50,00 | 950,00 |
| 2.500,00 | 5% | 125,00 | 2.375,00 |
| 500,00 | 5% | 25,00 | 475,00 |
| 0,00 | 5% | 0,00 | 0,00 |

---

## 6. Resumo dos Testes de Mesa

| TM | Algoritmo | Cenários Testados | Resultado |
|---|---|---|---|
| TM01 | Validação de CPF | 2 (válido + inválido) | ✅ |
| TM02 | Validação de Senha (comportamento real) | 7 cenários | ✅ |
| TM03 | Fluxo de Match | 5 cenários + máquina de estados | ✅ |
| TM04 | Taxas e Planos | 4 planos + 5 cenários de destaque + 4 transações | ✅ |

---

## 7. Histórico de Revisões

| Versão | Data | Autor | Alteração |
|---|---|---|---|
| 1.0 | 08/07/2026 | Arthur Iantas Stelzner | Criação inicial |
| 1.1 | 08/07/2026 | Arthur Iantas Stelzner | Removido TM03 (bloqueio de login) conforme RF01.5 removido dos requisitos. Renumerado TM04 → TM03 |
| 1.2 | 08/07/2026 | Arthur Iantas Stelzner | TM02 revisado: código não implementa validação de senha forte. Novo TM02 documenta comportamento real |
