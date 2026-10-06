<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PetAlliance</title>
    <link rel="stylesheet" href="/frontEnd/style/style.css">
    <link rel="stylesheet" href="/frontEnd/style/estilos.css">
</head>
<body>
    <nav class="navbar">
        <div class="navbar-left">
            <img src="/frontEnd/assets/images/logo.webp" alt="PetAlliance" class="navbar-logo">
            <span class="navbar-title">PetAlliance</span>
        </div>
        <div class="navbar-right">
            <a href="/backEnd/usuario/perfil.php">
<?php
  $img = $_SESSION['usuario_imagem'] ?? '';
  if ($img && $img !== 'placeholder.webp' && $img !== 'null'):
?>
              <img src="/uploads/usuario/<?= $img ?>" alt="" class="navbar-avatar">
<?php else:
  $nome = $_SESSION['usuario_nome'] ?? '';
  $inicial = $nome ? strtoupper(substr($nome, 0, 1)) : '';
?>
              <span class="navbar-avatar-initial">
<?php if ($inicial): ?>
                <?= $inicial ?>
<?php else: ?>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
<?php endif; ?>
              </span>
<?php endif; ?>
            </a>
            <a href="/backEnd/home.php" class="navbar-btn" title="Início">⌂</a>
            <a href="/backEnd/animal/favoritos.php" class="navbar-btn" title="Favoritos">♡</a>
            <button type="button" class="navbar-btn" commandFor="menu" command="show-modal" title="Menu">☰</button>
        </div>
    </nav>

    <dialog id="menu" class="dialog-menu">
        <div class="dialog-menu-header">
            <span class="dialog-menu-title">Menu</span>
            <button type="button" class="dialog-menu-close" commandFor="menu" command="close">✕</button>
        </div>
        <ul class="dialog-menu-list">
            <li><a href="/backEnd/animal/meusAnimais.php">Meus Animais</a></li>
            <li><a href="/backEnd/animal/cadastrarAnimal.php">Cadastrar Animal</a></li>
            <li><a href="/backEnd/usuario/membros.php">Membros</a></li>
            <li><a href="/backEnd/chat.php">Chat</a></li>
            <li><a href="/backEnd/match.php">Notificações</a></li>
            <li><a href="/backEnd/usuario/perfil.php">Perfil</a></li>
            <li><a href="/backEnd/usuario/configuracoes.php">Configurações</a></li>
<?php if (($_SESSION['usuario_tipo'] ?? 0) == 1): ?>
            <li class="dialog-menu-divider"></li>
            <li><a href="/backEnd/admin/denuncias.php">Ver Denúncias</a></li>
            <li><a href="/backEnd/admin/estatisticas.php">Estatísticas</a></li>
<?php endif; ?>
        </ul>
    </dialog>

    <script src="/frontEnd/utils/navBar.js"></script>