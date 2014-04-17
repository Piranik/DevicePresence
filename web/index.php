<?php
$app = require_once __DIR__ . '/../src/bootstrap.php';

$app->get(
    '/',
    function () use ($app) {
        return $app->redirect('/graph');
    }
);
$app->mount('/graph', new \App\Controller\Graph());
$app->mount('/devices', new \App\Controller\Devices());
$app->run();
