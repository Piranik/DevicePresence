<?php
$app = require __DIR__ . '/../src/bootstrap.php';

use Symfony\Component\Console\Application;

$cli = new Application('Device Presence', '1.0');
$cli->add($app['command.scannercommand']);

$cli->run();
