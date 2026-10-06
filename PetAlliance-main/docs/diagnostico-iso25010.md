# Diagnóstico de Qualidade — ISO/IEC 25010

## 1. Objetivo

Avaliar o sistema Pet Alliance sob as **8 características de qualidade** da norma ISO/IEC 25010 (SQuaRE), com base na análise teórica da arquitetura, tecnologias e código-fonte.

---

## 2. Escala de Avaliação

| Nota | Significado |
|---|---|
| 1 | Não atende |
| 2 | Atende parcialmente (falhas críticas) |
| 3 | Atende razoavelmente (melhorias necessárias) |
| 4 | Atende bem (pequenos ajustes) |
| 5 | Atende plenamente |

---

## 3. Diagnóstico por Característica

### 3.1. Adequação Funcional (Functional Suitability)

| Subcaracterística | Nota | Evidência / Justificativa |
|---|---|---|
| Completude funcional | 4 | 11 RFs mapeados, todos implementados conforme o código atual. Validação de senha forte (RF02.3) e limite de 8 caracteres (RF01.3) não implementados — documentados como gaps de requisitos |
| Corretude funcional | 3 | Login, cadastro, match, chat e pagamentos funcionam. Login aceita apenas CPF (não email) |
| Adequação funcional | 4 | Fluxos principais funcionam. Funcionalidades atendem ao propósito de conectar tutores |

**Média:** **3,7**

---

### 3.2. Eficiência de Performance (Performance Efficiency)

| Subcaracterística | Nota | Evidência / Justificativa |
|---|---|---|
| Comportamento temporal | 3 | Sem lazy loading, sem cache de queries. Busca de animais faz requisição síncrona sem indexação |
| Utilização de recursos | 4 | PHP sem framework pesado. CSS/JS inline em vários lugares, sem bundler |
| Capacidade | 3 | Sem prepared statements para carga (apenas para operações). Sem suporte explícito a paginação |

**Média:** **3,3**

---

### 3.3. Compatibilidade (Compatibility)

| Subcaracterística | Nota | Evidência / Justificativa |
|---|---|---|
| Coexistência | 4 | PHP/MySQL roda em servidor compartilhado sem conflito |
| Interoperabilidade | 4 | API REST para frontend (JSON). Integração com AbacatePay (API externa) e ViaCEP |

**Média:** **4,0**

---

### 3.4. Usabilidade (Usability)

| Subcaracterística | Nota | Evidência / Justificativa |
|---|---|---|
| Reconhecibilidade | 3 | Navbar clara, mas sem breadcrumbs. Página inicial sem onboarding |
| Apreensibilidade | 4 | Fluxos intuitivos (cadastro → login → match → chat) |
| Operabilidade | 3 | Carrossel de fotos apenas no hover (não funciona em mobile touch). Botões sem feedback visual claro |
| Proteção contra erros do usuário | 2 | Senha sem validação de força (comportamento esperado atual). Nenhuma confirmação em ações destrutivas (excluir conta) além de `confirm()` |
| Estética da interface | 3 | CSS básico, sem design system. NavBar com placeholder "NavBar aqui" e "Footer aqui" |
| Acessibilidade | 2 | Diversos problemas de acessibilidade (ver checklist específico) |

**Média:** **2,8**

---

### 3.5. Confiabilidade (Reliability)

| Subcaracterística | Nota | Evidência / Justificativa |
|---|---|---|
| Maturidade | 3 | Sistema operacional, mas com falhas de validação conhecidas |
| Disponibilidade | 3 | Sem monitoramento, sem redundância. 100% dependente do Apache + MySQL |
| Tolerância a falhas | 2 | Tratamento de exceções genérico (`catch Exception $e`). Sem fallback para APIs externas (AbacatePay offline quebra pagamento) |
| Recuperabilidade | 3 | Banco com `ON DELETE CASCADE`. Logout destrói sessão. Sem backup automático no código |

**Média:** **2,8**

---

### 3.6. Segurança (Security)

| Subcaracterística | Nota | Evidência / Justificativa |
|---|---|---|
| Confidencialidade | 4 | Senhas hash com `password_hash()`. Sessão PHP com `session_start()` |
| Integridade | 3 | Dados trafegam sem SSL (HTTP). SQL injection mitigado com prepared statements |
| Não-repúdio | 2 | Sem logs de auditoria de ações críticas (exclusão de conta, pagamentos) |
| Responsabilidade | 3 | Sessão identifica usuário. Admin tem `tipo_usuario = 1` |
| Autenticidade | 3 | Login via CPF + senha hash. Verificação de email obrigatória |

**Média:** **3,0**

---

### 3.7. Manutenibilidade (Maintainability)

| Subcaracterística | Nota | Evidência / Justificativa |
|---|---|---|
| Modularidade | 3 | Separação MVC (models/controllers/views). Mas views têm PHP inline misturado com HTML |
| Reusabilidade | 3 | DAOs reutilizáveis. Controllers com lógica duplicada em alguns pontos |
| Analisabilidade | 3 | Código sem comentários, sem padrão PSR, sem types declarados |
| Modificabilidade | 3 | Adicionar novo RF exige criar model + controller + view + rota |
| Testabilidade | 2 | Sem testes automatizados. Sem injeção de dependência |

**Média:** **2,8**

---

### 3.8. Portabilidade (Portability)

| Subcaracterística | Nota | Evidência / Justificativa |
|---|---|---|
| Adaptabilidade | 4 | PHP + MySQL roda em qualquer OS (Windows, Linux, Mac) |
| Capacidade de instalação | 4 | Requer apenas XAMPP + clonar repositório |
| Substituibilidade | 3 | Acoplado ao MySQL (queries nativas). Migração para outro BD exigiria reescrita |

**Média:** **3,7**

---

## 4. Resumo Geral

| Característica | Nota |
|---|---|
| Adequação Funcional | 3,7 |
| Eficiência de Performance | 3,3 |
| Compatibilidade | 4,0 |
| Usabilidade | 2,8 |
| Confiabilidade | 2,8 |
| Segurança | 3,0 |
| Manutenibilidade | 2,8 |
| Portabilidade | 3,7 |
| **Média Geral** | **3,3 / 5,0** |

---

## 5. Pontos Críticos (Nota ≤ 2,5)

| Característica | Nota | Problema |
|---|---|---|
| Usabilidade - Proteção contra erros | 2 | Sistema não valida senha forte (comportamento atual) |
| Usabilidade - Acessibilidade | 2 | Múltiplas barreiras de acessibilidade |
| Confiabilidade - Tolerância a falhas | 2 | APIs externas sem fallback |
| Manutenibilidade - Testabilidade | 2 | Sem testes automatizados |

---

## 6. Histórico de Revisões

| Versão | Data | Autor | Alteração |
|---|---|---|---|
| 1.0 | 08/07/2026 | Arthur Iantas Stelzner | Diagnóstico inicial baseado em análise teórica |
| 1.2 | 08/07/2026 | Arthur Iantas Stelzner | Ajustadas referências a validação de senha — alinhamento com código real |
