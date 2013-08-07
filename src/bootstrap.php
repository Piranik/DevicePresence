<?php
require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();
$app->register(new Igorw\Silex\ConfigServiceProvider(
    __DIR__ . "/../config/app/config.yml",
    array('root_path' => __DIR__ . '/../')
));
$app->register(new KevinGH\Entities\EntitiesServiceProvider());

return $app;
