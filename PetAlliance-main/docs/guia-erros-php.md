# Guia de Resolução de Erros PHP — PetAlliance

Guia de referência para diagnosticar e resolver os erros de ambiente PHP mais comuns no projeto (Windows). Use este documento quando o erro abaixo aparecer novamente — o passo a passo cobre diagnóstico, correção e verificação.

---

## 1. Erro: "Erro ao conectar ao banco de dados. Tente novamente mais tarde."

### 1.1 Sintoma

Erro fatal ao acessar qualquer página que usa banco (login, cadastro, home, etc.):

```
Erro fatal : Exceção não capturada InvalidArgumentException: Erro ao conectar ao banco de dados. Tente novamente mais tarde.
em ...\backEnd\config\config.php:47
```

O rastreamento da pilha aponta para `UsuarioDAO->__construct()` e `Conexao::getConexao()`.

**Importante:** essa mensagem é **genérica de propósito**. O `config.php` captura a `PDOException` real e joga uma mensagem amigável pro usuário. O erro verdadeiro vai para o `error_log` do PHP. Para ver a causa real durante o diagnóstico, use os comandos da seção 1.3.

> Com o banco configurado para **uso local** (credenciais editadas no `config.php`), o mesmo sintoma aparece também quando o **MySQL local está desligado** — nesse caso o `error_log` mostra `Connection refused` / `No such file or directory` ao invés de `could not find driver`.

### 1.2 Causa raiz

Na maioria dos casos a causa é **local**, não é o servidor:

1. O PHP está rodando **sem `php.ini`** (ou com `extension_dir` apontando para uma pasta que não existe, ex.: `C:\php\ext`).
2. Sem o `php.ini` correto, o driver **`pdo_mysql` não é carregado**.
3. `new PDO("mysql:...")` lança `PDOException: could not find driver`.
4. O `catch` do `config.php` transforma isso na mensagem genérica do sintoma.

### 1.3 Diagnóstico (executar nesta ordem)

```powershell
# 1. O php.ini está carregado?
php --ini
#   Se mostrar "Loaded Configuration File: (none)" -> é isso. Ir para 1.4.

# 2. O driver pdo_mysql está carregado?
php -m
#   Deve listar: pdo_mysql (e mysqlnd, PDO). Se não listar -> é isso.

# 3. Quais drivers PDO existem?
php -r "print_r(PDO::getAvailableDrivers());"
#   Deve retornar Array ( [0] => mysql ... ). Vazio = driver não carregado.

# 4. Para onde o extension_dir aponta?
php -i | Select-String 'extension_dir'
#   Deve apontar para a pasta ext DENTRO da instalação do PHP.
```

### 1.4 Solução passo a passo

**Passo 1 — Localizar a instalação do PHP:**

```powershell
(Get-Command php).Source
# Exemplo: C:\Senai\php-8.4.5-Win32-vs17-x64\php.exe
```

**Passo 2 — Criar o `php.ini` a partir do template (se não existir):**

```powershell
$dir = "C:\caminho\para\php"   # pasta do passo 1, SEM o php.exe
Copy-Item "$dir\php.ini-development" "$dir\php.ini"
```

> Use `php.ini-development` para ambiente de desenvolvimento (erros visíveis) e `php.ini-production` para produção.

**Passo 3 — Editar o `php.ini` criado:**

- `extension_dir` — apontar para a pasta `ext` **com caminho absoluto** (caminho relativo quebra dependendo de onde o PHP é chamado):

  ```ini
  extension_dir = "C:\caminho\para\php\ext"
  ```

- Descomentar (remover o `;` do início) as extensões usadas pelo projeto:

  ```ini
  extension=pdo_mysql
  extension=mysqli
  extension=curl
  extension=openssl
  extension=mbstring
  extension=gd
  extension=fileinfo
  extension=sqlite3
  extension=pdo_sqlite
  ```

  > `curl` e `openssl` são necessários para o PHPMailer (e-mails) e para as chamadas da API de pagamento. Antes de habilitar, confira se a DLL existe: `Test-Path "$dir\ext\php_$ext.dll"`.

- Recomendado (previne outros dois erros — ver seções 2 e 3):

  ```ini
  output_buffering = 4096
  date.timezone = America/Sao_Paulo
  ```

> **Atenção:** o arquivo deve ficar em **UTF-8 sem BOM** e com quebras de linha normais. Se o `extension_dir` não for reconhecido, o PHP volta ao padrão compilado `C:\php\ext` e o erro reaparece.

**Passo 4 — Validar:**

```powershell
php --ini          # deve mostrar o php.ini recém-criado
php -m             # deve listar pdo_mysql SEM warnings de startup
php -r "print_r(PDO::getAvailableDrivers());"   # deve listar mysql
```

**Passo 5 — Testar a conexão com o banco:**

```powershell
php -r "try { new PDO('mysql:host=SEU_HOST;dbname=SEU_BANCO;charset=utf8','USUARIO','SENHA'); echo 'OK'; } catch (PDOException \$e) { echo \$e->getMessage(); }"
```

Se conectar, o erro fatal some. Se falhar com outra mensagem (ex.: `Access denied`, `Unknown database`, `Connection refused`), o problema é de credenciais/banco — confira as linhas 35–39 do `config.php` (`backEnd/config/config.php`).

> **Atenção:** o `config.php` versionado contém **apenas credenciais locais** (`localhost`/`root`) — sem segredos. Membros da equipe preenchem as credenciais do banco remoto no arquivo **local** (linhas 35–39); as edições locais ficam fora do git rodando uma vez `git update-index --skip-worktree backEnd/config/config.php`.

### 1.5 Como ver o erro REAL escondido pelo config.php

O `config.php` já registra o erro real no log do PHP (`error_log("Erro de conexão: " . $e->getMessage())`). Para ver:

```powershell
# Local padrão do error_log em instalações Windows sem config:
#   C:\caminho\para\php\logs\php_error.log  (depende do php.ini)
```

Ou, temporariamente, em `backEnd/config/config.php` linha 47, troque por:

```php
throw new InvalidArgumentException("Erro ao conectar: " . $e->getMessage());
```

**Não deixe essa versão em produção** — ela expõe detalhes do servidor para o usuário.

---

## 2. Erro: "Session cannot be started after headers have already been sent"

### 2.1 Sintoma

```
Warning: session_start(): Session cannot be started after headers have already been sent
in ...\index.php on line 1
```

### 2.2 Causa raiz

O arquivo começa com **BOM UTF-8** (`EF BB BF`) — 3 bytes invisíveis gravados pelo editor ao salvar como "UTF-8 with BOM". Como o `output_buffering` estava desligado, esses bytes eram enviados ao navegador **antes** de qualquer código PHP rodar; quando `session_start()` executava, os headers já tinham sido enviados.

### 2.3 Diagnóstico

Verificar os primeiros bytes do arquivo (deve começar com `3C 3F 70` = `<?php`):

```powershell
$bytes = [System.IO.File]::ReadAllBytes("C:\caminho\index.php")
$bytes[0..2] | ForEach-Object { '{0:X2}' -f $_ }
# EF BB BF = tem BOM (problema) | 3C 3F 70 = ok
```

### 2.4 Solução — remover o BOM de todos os arquivos PHP do projeto

```powershell
$root = "C:\caminho\projeto"
Get-ChildItem -Path $root -Filter *.php -Recurse -File | Where-Object { $_.FullName -notmatch '\\vendor\\' } | ForEach-Object {
    $b = [System.IO.File]::ReadAllBytes($_.FullName)
    if ($b.Length -ge 3 -and $b[0] -eq 0xEF -and $b[1] -eq 0xBB -and $b[2] -eq 0xBF) {
        [System.IO.File]::WriteAllBytes($_.FullName, $b[3..($b.Length-1)])
        Write-Output ("BOM removido: " + $_.FullName)
    }
}
```

> Além do `session_start`, o BOM também quebra respostas JSON (as APIs do `backEnd/controllers/api/`) e webhooks (`abacatepay-webhook.php`).

### 2.5 Prevenção

- No editor, salvar sempre como **UTF-8 sem BOM** (VS Code: `UTF-8` na barra inferior; Notepad++: `Encoding → Encode in UTF-8`).
- Manter `output_buffering = 4096` no `php.ini` — segura qualquer saída acidental (espaços/linhas em branco antes do `<?php`).

---

## 3. Checklist rápido sintoma → causa → correção

| Sintoma | Causa provável | Correção |
|---|---|---|
| Fatal: "Erro ao conectar ao banco de dados..." | `pdo_mysql` não carregado (sem php.ini ou `extension_dir` errado) | Seção 1.4 |
| `session_start(): headers already sent` | BOM UTF-8 no início do arquivo | Seção 2.4 |
| `could not find driver` no error_log | `extension=pdo_mysql` descomentado? DLL existe? | Seções 1.4 (passo 3) |
| JSON da API retorna erro de parse no front | BOM no arquivo do controller | Seção 2.4 |
| `Warning: PHP Startup: Unable to load dynamic library '...' (tried: C:\php\ext\...)` | `extension_dir` não foi aplicado (php.ini ausente ou linha inválida) | Seção 1.4 (passo 3) |

---

## 4. Comandos de referência rápida

```powershell
php --ini                                  # php.ini carregado?
php -m                                     # extensões ativas
php -v                                     # versão
php -r "print_r(PDO::getAvailableDrivers());"   # drivers PDO disponíveis
php -i | Select-String 'extension_dir'     # para onde aponta o extension_dir
(Get-Command php).Source                   # caminho do php.exe em uso
```

---

## 5. Histórico de Revisões

| Versão | Data | Autor | Alteração |
|---|---|---|---|
| 1.0 | 13/08/2026 | Carlos Eduardo Duhring | Criação do guia — documenta erro de `pdo_mysql`/php.ini e BOM UTF-8 ocorridos em ambiente Windows (PHP 8.4.5 em `C:\Senai\`), corrigidos no commit `ca5800b` |
| 1.1 | 13/08/2026 | Carlos Eduardo Duhring | Ajustes: `config.php` sai do versionamento (`.gitignore`) — notas de diagnóstico atualizadas para credenciais locais editadas no próprio arquivo |
| 1.2 | 13/08/2026 | Carlos Eduardo Duhring | `config.php` versionado novamente, **sem credenciais** (padrão banco local); equipe preenche credenciais remotas localmente com `--skip-worktree` |
