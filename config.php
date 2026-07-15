<?php

return [
    'app_name' => 'Rokola RitmoPTY',
    'base_url' => '/RokolaRitmoPTY/public',
    'db' => [
        'host' => '127.0.0.1',
        'name' => 'rokola_ritmopty',
        'user' => 'root',
        'pass' => 'demo',
        'charset' => 'utf8mb4',
    ],
    'free_monthly_limit' => 20,
    'upload_dir' => __DIR__ . '/public/uploads',
    'debug' => false,
    'log_file' => __DIR__ . '/storage/logs/app.log',
];
