<?php
    $PLANOS = [
        getenv('ABACATEPAY_PRODUCT_ID') => [
            'nome' => 'Iniciante',
            'max_destaques' => 5,
            'preco' => 7.50
        ],
        getenv('ABACATEPAY_PRODUCT2_ID') => [
            'nome' => 'Básico',
            'max_destaques' => 10,
            'preco' => 15.00
        ],
        getenv('ABACATEPAY_PRODUCT3_ID') => [
            'nome' => 'Profissional',
            'max_destaques' => 20,
            'preco' => 30.00
        ],
        getenv('ABACATEPAY_PRODUCT4_ID') => [
            'nome' => 'Premium',
            'max_destaques' => 30,
            'preco' => 45.00
        ]
    ];
