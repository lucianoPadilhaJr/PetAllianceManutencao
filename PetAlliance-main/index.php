<?php
session_start();

$diretorioUsuario = 'uploads/usuario';
$diretorioAnimal = 'uploads/animais';

if (!is_dir($diretorioUsuario)) {
    mkdir($diretorioUsuario, 0755, true);
}

if (!is_dir($diretorioAnimal)) {
    mkdir($diretorioAnimal, 0755, true);
}

require __DIR__ . '/frontEnd/views/index.html';
