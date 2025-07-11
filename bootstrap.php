<?php
// bootstrap.php

// 1. Include the configuration file to get the BASE_URL
require_once __DIR__ . '/config.php';

// 2. Composer's Autoloader to load all the libraries
require_once __DIR__ . '/vendor/autoload.php';

// 3. Import necessary classes for Eloquent ORM
use Illuminate\Database\Capsule\Manager as Capsule;

// 4. Create a new instance of the Capsule manager (for Eloquent)
$capsule = new Capsule;

// 5. Add database connection details
$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => 'localhost',
    'database'  => 'devdost_db',
    'username'  => 'root',
    'password'  => '', // Default password for XAMPP is empty
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
]);

// 6. Make this Capsule instance available globally via static methods
$capsule->setAsGlobal();

// 7. Boot up Eloquent ORM
$capsule->bootEloquent();

?>