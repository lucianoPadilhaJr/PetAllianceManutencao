<?php

    session_start();

    if(!$_SESSION['usuario_id']) {
        header('Location: /index.php');
        exit();
    }

    require __DIR__ . "/../views/navBar.php";
    require __DIR__ . "/../../frontEnd/views/configuracoes.html";
    require __DIR__ . "/../../frontEnd/views/footer.html";