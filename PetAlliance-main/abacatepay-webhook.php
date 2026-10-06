<?php
    require_once __DIR__ . "/backEnd/config/config.php";
    require_once __DIR__ . "/backEnd/controllers/api/PagamentoController.php";

    $controller = new PagamentoController();
    $controller->webhook();