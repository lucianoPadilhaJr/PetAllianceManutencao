<?php

session_start();

// Remove todas as variáveis da sessão
$_SESSION = [];

// Destrói a sessão
session_destroy();

// Redireciona o usuário
header("Location: /index.php");
exit;