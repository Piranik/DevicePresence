<?php
require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();
$app['debug'] = true;

$app->get(
    '/',
    function () use ($app) {
        $scanner = new \App\Scan();
        return '<pre>' . print_r($scanner->scan(), true);
    }
);

// $app->get(
    // '/switch/{id}/{state}',
    // function ($id, $state) use ($app) {
        // $ls = new \App\Send\LightSwitch();
//
        // return sprintf(
            // 'Switching unit %u in state %s: %s',
            // $app->escape($id),
            // $app->escape($state),
            // $app->escape($ls->execute($id, $state))
        // );
    // }
// )
// ->assert('id', '\d+')
// ->assert('state', '(on|off)');

$app->run();
