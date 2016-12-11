<?php
$router = new \Phalcon\Mvc\Router();

$router->add(
    "/ipv4",
    [
        "controller" => "ipv4",
        "action"     => "index",
    ]
);

$router->handle();

return $router;