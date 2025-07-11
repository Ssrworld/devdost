<?php

return
[
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/db/migrations',
        'seeds' => '%%PHINX_CONFIG_DIR%%/db/seeds'
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment' => 'development',
        'development' => [
            'adapter' => 'mysql',
            'host' => 'localhost',
            'name' => 'devdost_db', // सुनिश्चित करें कि यह DB आपने phpMyAdmin में बनाया है
            'user' => 'root',
            'pass' => '', // XAMPP का पासवर्ड खाली होता है
            'port' => '3306',
            'charset' => 'utf8',
        ],
        // प्रोडक्शन के लिए सेटिंग्स हम बाद में डालेंगे
        'production' => [
            'adapter' => 'mysql',
            'host' => 'YOUR_PROD_HOST',
            'name' => 'YOUR_PROD_DB',
            'user' => 'YOUR_PROD_USER',
            'pass' => 'YOUR_PROD_PASS',
            'port' => '3306',
            'charset' => 'utf8',
        ]
    ],
    'version_order' => 'creation'
];