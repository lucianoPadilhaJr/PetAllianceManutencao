# Cenários em Gherkin (BDD)

## 1. Objetivo

Descrever cenários de teste usando a sintaxe **Gherkin** (Dado / Quando / Então) para transformar requisitos em especificações executáveis e legíveis por todos os stakeholders.

---

## 2. Legenda

```
Funcionalidade: <nome da funcionalidade>
  Como <papel>
  Eu quero <ação>
  Para <benefício>

  Cenário: <título do cenário>
    Dado <contexto / pré-condição>
    Quando <ação do usuário / evento>
    Então <resultado esperado>
```

---

## 3. Cenários

---

### US01 — Autenticação de Usuário

```gherkin
Funcionalidade: Autenticação de Usuário
  Como um usuário não logado
  Eu quero fazer login no sistema
  Para acessar minha conta e usar a plataforma

  Cenário: Login com credenciais válidas
    Dado que existe um usuário cadastrado com CPF "12345678901" e senha "Senha123"
    Quando eu informo CPF "12345678901" e senha "Senha123" no formulário de login
    E clico em "Entrar"
    Então sou redirecionado para a página inicial
    E meu nome aparece no cabeçalho

  Cenário: Login com CPF inexistente
    Dado que não existe usuário com CPF "00000000000"
    Quando eu informo CPF "00000000000" e senha "Senha123"
    E clico em "Entrar"
    Então vejo a mensagem "Usuário não encontrado"

  Cenário: Login com senha incorreta
    Dado que existe um usuário cadastrado com CPF "12345678901" e senha "Senha123"
    Quando eu informo CPF "12345678901" e senha "SenhaErrada"
    E clico em "Entrar"
    Então vejo a mensagem "Senha incorreta"

  Cenário: Recuperação de senha por email
    Dado que existe um usuário com email "usuario@email.com"
    Quando eu clico em "Esqueci minha senha"
    E informo o email "usuario@email.com"
    Então recebo um email com link/código para redefinir a senha
    E o código expira em 30 minutos

  Cenário: Redefinir senha com código válido
    Dado que solicitei recuperação de senha para "usuario@email.com"
    E recebi um código de 6 dígitos
    Quando eu informo o código recebido
    E crio uma nova senha "NovaSenha456"
    Então minha senha é atualizada
    E consigo fazer login com a nova senha
```

---

### US02 — Cadastro de Usuário

```gherkin
Funcionalidade: Cadastro de Usuário
  Como um novo usuário
  Eu quero me cadastrar na plataforma
  Para criar minha conta e usar o sistema

  Cenário: Cadastro com dados válidos
    Dado que estou na página de cadastro
    Quando preencho nome "João", username "joao", email "joao@email.com",
      CPF "52998224725", CEP "01001000", senha "MinhaSenha1" e confirmação "MinhaSenha1"
    E clico em "Cadastrar"
    Então minha conta é criada com sucesso
    E recebo um email de verificação

  Cenário: Cadastro com CPF duplicado
    Dado que já existe um usuário com CPF "52998224725"
    Quando tento cadastrar com o mesmo CPF "52998224725"
    Então vejo a mensagem "CPF já cadastrado"

  Cenário: Cadastro com email duplicado
    Dado que já existe um usuário com email "joao@email.com"
    Quando tento cadastrar com o mesmo email "joao@email.com"
    Então vejo a mensagem "Email já cadastrado"

```

---

### US03 — Cadastro de Pet

```gherkin
Funcionalidade: Cadastro de Pet
  Como um usuário logado
  Eu quero cadastrar meus animais
  Para encontrar matches e vender filhotes

  Cenário: Cadastro de pet com dados completos
    Dado que estou logado como usuário "joao"
    Quando acesso a página de cadastro de animal
    E preencho nome "Rex", raça "Pastor Alemão", cor "Marrom",
      sexo "Macho", tipo "Cachorro", porte "Grande", idade "2 anos",
      peso "35.5", descrição "Cão dócil e vacinado"
    E faço upload de 1 foto
    E marco "Vacinado" como verdadeiro
    E clico em "Cadastrar"
    Então o pet "Rex" é cadastrado com sucesso
    E ele aparece na listagem de animais

  Cenário: Cadastro sem foto
    Dado que estou logado como usuário "joao"
    Quando preencho todos os dados do pet
    E não faço upload de nenhuma foto
    E clico em "Cadastrar"
    Então vejo a mensagem "Foto é obrigatória"

  Cenário: Upload de mais de 10 fotos
    Dado que estou logado como usuário "joao"
    Quando tento fazer upload de 11 fotos
    Então o sistema aceita apenas as 10 primeiras
    E exibe a mensagem "Limite máximo de 10 fotos"

  Cenário: Editar pet por outro usuário
    Dado que existe um pet "Rex" pertencente ao usuário "joao"
    Quando o usuário "maria" tenta editar o pet "Rex"
    Então o sistema bloqueia a edição
    E exibe a mensagem "Você não tem permissão para editar este animal"
```

---

### US04 — Listagem e Busca de Pets

```gherkin
Funcionalidade: Listagem e Busca de Pets
  Como um usuário logado
  Eu quero visualizar e buscar animais cadastrados
  Para encontrar parceiros para meu pet

  Cenário: Listar todos os animais
    Dado que estou logado como usuário "joao"
    E existem 10 animais cadastrados por outros usuários
    Quando acesso a página inicial
    Então vejo a lista de animais disponíveis
    E meus próprios animais não aparecem na lista

  Cenário: Filtrar por tipo
    Dado que existem animais dos tipos "Cachorro", "Gato" e "Cavalo"
    Quando seleciono o filtro "Cachorro"
    Então vejo apenas animais do tipo "Cachorro"

  Cenário: Buscar por nome
    Dado que existe um animal chamado "Rex"
    Quando digito "Rex" na busca
    Então vejo o animal "Rex" nos resultados
```

---

### US05 — Match de Animais

```gherkin
Funcionalidade: Match de Animais
  Como um usuário logado com pet cadastrado
  Eu quero enviar e receber solicitações de match
  Para conectar meu animal com outros da plataforma

  Cenário: Enviar solicitação de match
    Dado que estou logado como usuário "joao"
    E tenho o pet "Rex" cadastrado
    E existe o pet "Luna" do usuário "maria"
    Quando clico em "Enviar Match" para o pet "Luna"
    Então uma solicitação é enviada para "maria"
    E vejo a mensagem "Solicitação enviada!"

  Cenário: Match sem pet cadastrado
    Dado que estou logado como usuário "joao"
    E não tenho nenhum pet cadastrado
    Quando clico em "Enviar Match"
    Então vejo a mensagem "Você precisa cadastrar um pet primeiro"

  Cenário: Aceitar solicitação de match
    Dado que "joao" enviou uma solicitação de match para "maria"
    Quando "maria" acessa as solicitações recebidas
    E clica em "Aceitar"
    Então o match é ativado
    E o chat entre "joao" e "maria" é liberado

  Cenário: Recusar solicitação de match
    Dado que "joao" enviou uma solicitação de match para "maria"
    Quando "maria" clica em "Recusar"
    Então a solicitação é recusada
    E "joao" recebe uma notificação de recusa

  Cenário: Desfazer match
    Dado que "joao" e "maria" têm um match ativo
    Quando "joao" clica em "Desfazer Match"
    Então o match é removido
    E o chat entre eles é desativado

  Cenário: Bloquear usuário
    Dado que "maria" está incomodando "joao"
    Quando "joao" bloqueia "maria"
    Então "maria" não pode mais enviar solicitações de match para "joao"
    E "maria" não pode mais ver o perfil de "joao"
```

---

### US06 — Chat

```gherkin
Funcionalidade: Chat
  Como um usuário com match ativo
  Eu quero conversar com o dono do outro pet
  Para combinar detalhes do acasalamento ou venda

  Cenário: Enviar mensagem no chat
    Dado que "joao" e "maria" têm um match ativo
    Quando "joao" abre o chat com "maria"
    E digita "Olá, tudo bem?" no campo de mensagem
    E clica em "Enviar"
    Então a mensagem aparece no chat
    E "maria" recebe a mensagem em tempo real

  Cenário: Chat sem match ativo
    Dado que "joao" não tem match com "maria"
    Quando "joao" tenta acessar o chat com "maria"
    Então o sistema bloqueia o acesso
    E exibe a mensagem "Match necessário para usar o chat"

  Cenário: Mensagens com timestamp
    Dado que "joao" enviou uma mensagem para "maria"
    Quando "maria" visualiza a mensagem
    Então ela vê a data e hora do envio

  Cenário: Excluir conversa
    Dado que "joao" e "maria" têm uma conversa ativa
    Quando "joao" clica em "Excluir Conversa"
    Então a conversa é removida da lista de "joao"
```

---

### US07 — Venda de Filhotes

```gherkin
Funcionalidade: Venda de Filhotes
  Como dono de um pet
  Eu quero anunciar filhotes para venda
  Para comercializar meus animais na plataforma

  Cenário: Criar anúncio de venda
    Dado que estou logado como "joao" e sou dono do pet "Rex"
    Quando edito o pet "Rex" e defino preço "R$ 2.500,00"
    Então o pet aparece com selo "À venda" na listagem
    E o preço é exibido nos detalhes

  Cenário: Editar preço do anúncio
    Dado que o pet "Rex" está anunciado por "R$ 2.500,00"
    Quando edito o preço para "R$ 2.000,00"
    Então o novo preço é exibido

  Cenário: Vender pet de outro usuário
    Dado que o pet "Rex" pertence a "joao"
    Quando "maria" tenta definir preço para "Rex"
    Então o sistema bloqueia
    E exibe "Você não é o dono deste animal"
```

---

### US08 — Pagamentos

```gherkin
Funcionalidade: Pagamentos
  Como um usuário
  Eu quero realizar pagamentos via plataforma
  Para adquirir planos de destaque e concluir transações

  Cenário: Criar checkout de pagamento
    Dado que estou logado como "joao"
    Quando seleciono o plano "Básico" (R$ 15,00)
    E confirmo a compra
    Então sou redirecionado para o checkout do AbacatePay
    E um registro de pagamento "PENDING" é criado no banco

  Cenário: Webhook confirma pagamento
    Dado que existe um pagamento pendente no banco
    Quando o AbacatePay envia notificação de "PAID" via webhook
    Então o status no banco é atualizado para "PAID"
    E o usuário recebe notificação de confirmação

  Cenário: Visualizar histórico de pagamentos
    Dado que o usuário "joao" já realizou 3 pagamentos
    Quando "joao" acessa a página de pagamentos
    Então ele vê uma lista com os 3 pagamentos
    E cada pagamento exibe data, valor e status

  Cenário: Cancelar pagamento pendente
    Dado que o usuário "joao" tem um pagamento com status "PENDING"
    Quando "joao" clica em "Cancelar"
    Então o status é alterado para "CANCELLED"
```

---

## 4. Histórico de Revisões

| Versão | Data | Autor | Alteração |
|---|---|---|---|
| 1.0 | 08/07/2026 | Arthur Iantas Stelzner | Criação inicial |
| 1.1 | 08/07/2026 | Arthur Iantas Stelzner | Removidos cenários de bloqueio de login (RF01.5) e CAPTCHA (RF02.4). Atualizado limite de fotos para 10 |
| 1.2 | 08/07/2026 | Arthur Iantas Stelzner | Removido cenário "Cadastro com senha fraca" — código não implementa validação de senha forte |
