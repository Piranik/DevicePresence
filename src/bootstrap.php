<?php
require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();
$app->register(new Igorw\Silex\ConfigServiceProvider(
    __DIR__ . "/../config/app/config.yml",
    array('root_path' => __DIR__ . '/../')
));
$app->register(new KevinGH\Entities\EntitiesServiceProvider());
$app->register(new Silex\Provider\TwigServiceProvider(), $app['twig.config']);

$app['es'] = $app->share(function () {
    return new \Elastica\Client(
            array(
                'host' => '127.0.0.1',
                'port' => 9200,
            )
        );
});

return $app;
