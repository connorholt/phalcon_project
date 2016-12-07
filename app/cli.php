<?php
use Phalcon\Di\FactoryDefault\Cli as CliDI;
use Phalcon\Cli\Console as ConsoleApp;
use Phalcon\Loader;

// Используем стандартный для CLI контейнер зависимостей
$di = new CliDI();

/**
 * Регистрируем автозагрузчик, и скажем ему, чтобы зарегистрировал каталог задач
 */
$loader = new Loader();

$loader->registerDirs(
    [
        __DIR__ . "/tasks",
    ]
);

$loader->register();

// Загружаем файл конфигурации, если он есть
$configFile = __DIR__ . "/config/config.php";

if (is_readable($configFile)) {
    $config = include $configFile;

    $di->set("config", $config);
}

$di->set('db', function () use ($config) {

    $config = array(
        'host'     => $config->database->host,
        'username' => $config->database->username,
        'password' => $config->database->password,
        'dbname'   => $config->database->dbname
    );

    return new \Phalcon\Db\Adapter\Pdo\Postgresql($config);
});


// Создаем консольное приложение
$console = new ConsoleApp();
$console->setDI($di);

/**
 * Определяем консольные аргументы
 */
$arguments = [];

foreach ($argv as $k => $arg) {
    if ($k === 1) {
        $arguments["task"] = $arg;
    } elseif ($k === 2) {
        $arguments["action"] = $arg;
    } elseif ($k >= 3) {
        $arguments["params"][] = $arg;
    }
}


try {
    // обрабатываем входящие аргументы
    $console->handle($arguments);
} catch (\Phalcon\Exception $e) {
    echo $e->getMessage();

    exit(255);
}