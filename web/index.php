<?php
$app = require_once __DIR__ . '/../src/bootstrap.php';

$app->get(
    '/',
    function () use ($app) {
        return 'Device Presence Webinterface (coming soon)';
    }
);

$app->run();
