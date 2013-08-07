<?php
$app = require_once __DIR__ . '/../src/bootstrap.php';

$app->get(
    '/',
    function () use ($app) {
        $scanner = new \App\Scan($app['em'], $app['scan.options']);
        return sprintf('Found %u online devices', count($scanner->scan()));
    }
);

$app->run();
