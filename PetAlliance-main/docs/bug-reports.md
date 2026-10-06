# Bug Reports — Relatório de Defeitos

## 1. Objetivo

Registrar os defeitos encontrados durante a inspeção do código em relação aos requisitos funcionais (SRS). Cada bug report contém passos para reprodução, severidade, prioridade e evidências.

---

## 2. Legenda

| Campo | Descrição |
|---|---|
| **BUG-ID** | Identificador único do defeito |
| **RF** | Requisito Funcional violado |
| **Severidade** | Blocker / Crítica / Alta / Média / Baixa |
| **Prioridade** | Imediata / Alta / Média / Baixa |
| **Status** | Open / In Progress / Fixed / Closed / Rejected |
| **Ambiente** | Desenvolvimento / Homologação / Produção |

---

## 3. Nota sobre divergências SRS vs. Código

A inspeção do código identificou divergências entre os requisitos documentados no SRS e a implementação atual. Estas divergências **não são registradas como bugs**, pois o código funciona conforme foi implementado — são gaps de requisitos que não foram codificados:

| SRS | O que o SRS diz | O que o código faz |
|---|---|---|
| RF01.3 | Senha com mínimo 8 caracteres | Login com `minlength="3"`, sem validação backend |
| RF02.3 | Senha deve conter maiúscula, minúscula e número | Cadastro aceita qualquer senha |
| RF01 | Login aceita Email/Username, CPF e Senha | Login aceita apenas CPF |
| RF01.3 (redefinição) | Senha com mínimo 8 caracteres | Redefinição valida mínimo 3 caracteres |

Esses gaps podem ser tratados como **melhorias futuras** no plano de evolução do sistema.

---

## 4. Métricas

| Métrica | Valor |
|---|---|
| Total de defeitos funcionais | 0 |
| Divergências SRS vs. código (não implementadas) | 4 |

---

## 5. Histórico de Revisões

| Versão | Data | Autor | Alteração |
|---|---|---|---|
| 1.0 | 08/07/2026 | Arthur Iantas Stelzner | Criação com divergências SRS vs. Código |
| 1.2 | 08/07/2026 | Arthur Iantas Stelzner | Bugs reclassificados como gaps de requisitos — alinhamento com código real |
