<?php

return [

    'currencies_separator' => '/',

    'sources' => [
        'test' => [
            'name' => 'Тестовые данные',
            'class' => \App\Sources\ExchangeTest::class,
            'params' => [],
        ],
        'binance' => [
            'name' => 'Биржа Binance',
            'class' => \App\Sources\ExchangeBinance::class,
            'params' => ['enableRateLimit' => true],
        ],
    ],

    'default_source' => env('DEFAULT_SOURCE', 'test'),




    'methods' => [
        'dijktra' => [
            'name' => 'Метод Дейкстера',
            'class' => \App\FinderMethods\DijkstraFinder::class,
        ],
        'direct' => [
            'name' => 'Прямой обмен',
            'class' => \App\FinderMethods\DirectFinder::class,
        ],
    ],

    'default_method' => env('DEFAULT_METHOD', 'dijktra'),

    'deals' => [
        'direct',
        'reverse',
    ],

    'bad_paths' => 4,
];
