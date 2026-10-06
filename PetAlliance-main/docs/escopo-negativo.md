# Escopo Negativo — Cláusula de Exclusão

## 1. Objetivo

Este documento lista de forma explícita **o que NÃO será testado** no ciclo atual de testes funcionais do Pet Alliance. O objetivo é alinhar expectativas com o cliente e evitar *scope creep* (expansão não controlada do escopo).

---

## 2. Itens Fora do Escopo de Teste

### 2.1. Testes Não-Funcionais

| Item | Motivo |
|---|---|
| **Teste de Performance/Carga** | Não há ferramenta de carga configurada; ambiente de dev não reflete produção |
| **Teste de Segurança (Pentest)** | Não há orçamento nem escopo para auditoria de segurança neste ciclo |
| **Teste de Estresse** | O ambiente atual não suporta simulação de picos |
| **Teste de Disponibilidade (24/7)** | Não é possível validar em ambiente de desenvolvimento |

### 2.2. Infraestrutura e DevOps

| Item | Motivo |
|---|---|
| **Backup e Recuperação** | Não faz parte dos requisitos funcionais |
| **Configuração de Servidor** | Responsabilidade da equipe de infra |
| **SSL / HTTPS** | Certificado não configurado no ambiente de dev |

### 2.3. Integrações Externas (Profundidade Limitada)

| Item | Motivo |
|---|---|
| **API AbacatePay — fluxos completos** | Testaremos apenas os fluxos felizes (checkout criado, webhook recebido). Fluxos de erro, reembolso e expiração serão simulados |
| **PHPMailer — entrega real** | Testaremos o envio da requisição, não a entrega efetiva no provedor de email |

### 2.4. Funcionalidades Não Implementadas

| Item | Motivo |
|---|---|
| **Notificações push / SMS** | Não implementado no sistema |
| **Aplicativo mobile nativo** | O sistema é web responsivo, não há app nativo |
| **Integração com redes sociais** | Não implementada |
| **Geolocalização em tempo real** | Não implementada |
| **Multi-idioma** | O sistema está apenas em português (PT-BR) |

### 2.5. Navegadores e Dispositivos

| Item | Motivo |
|---|---|
| **Internet Explorer** | Fora de suporte desde 2022 |
| **Safari (versões antigas)** | Testaremos apenas a versão mais recente |
| **Navegadores mobile muito antigos** | Android < 10, iOS < 14 |

### 2.6. Banco de Dados

| Item | Motivo |
|---|---|
| **Migração de dados** | Não aplicável |
| **Stored Procedures / Triggers** | O banco não utiliza |
| **Otimização de queries** | Escopo de desempenho, fora do funcional |

### 2.7. Acessibilidade

| Item | Motivo |
|---|---|
| **WCAG (leitor de tela, contraste, navegação por teclado)** | Não há requisito contratual de acessibilidade neste ciclo |

---

## 3. Observações Importantes

1. **Itens fora do escopo podem ser testados** se houver disponibilidade de tempo e recursos, mas **não são obrigatórios** para a aceitação do ciclo.
2. Caso o cliente deseje incluir algum item deste documento no escopo, deve ser feita uma **solicitação formal de mudança**, com reavaliação de prazo e custo.
3. Este documento deve ser revisado a cada novo ciclo de testes.

---

## 4. Histórico de Revisões

| Versão | Data | Autor | Alteração |
|---|---|---|---|
| 1.0 | 08/07/2026 | Arthur Iantas Stelzner | Criação inicial |
| 1.1 | 08/07/2026 | Arthur Iantas Stelzner | Removida referência a CAPTCHA (requisito excluído) |
