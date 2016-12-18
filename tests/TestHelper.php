<?php

use Phalcon\Di;
use Phalcon\Di\FactoryDefault;
use Phalcon\Loader;

ini_set("display_errors", 1);
error_reporting(E_ALL);

define("ROOT_PATH", __DIR__);
define('PATH_INCUBATOR', __DIR__ . '/../vendor/incubator/');
define('PATH_CONFIG', __DIR__ . '/../app/config/config.php');
define('PATH_MODELS', __DIR__ . '/../app/models/');

set_include_path(
    ROOT_PATH . PATH_SEPARATOR . get_include_path()
);

// требуется для phalcon/incubator
include __DIR__ . "/../../vendor/autoload.php";

// Используем автозагрузчик приложений для автозагрузки классов.
// Автозагрузка зависимостей, найденных в composer.
$loader = new Loader();

$loader->registerDirs(
    [
        ROOT_PATH,
        PATH_CONFIG,
        PATH_MODELS
    ]
);

$loader->register();

$di = new FactoryDefault();

Di::reset();


Di::setDefault($di);
