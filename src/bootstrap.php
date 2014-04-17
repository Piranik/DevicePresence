<?php
require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();
$app->register(new Igorw\Silex\ConfigServiceProvider(
    __DIR__ . "/../config/app/config.yml",
    array('root_path' => __DIR__ . '/../')
));
$app->register(new KevinGH\Entities\EntitiesServiceProvider());
$app->register(new Silex\Provider\TwigServiceProvider(), $app['twig.config']);

// External
$app['elasticsearch'] = $app->share(function () use ($app) {
    return new \Elastica\Client($app['elasticsearch.options']);
});

// Aggregation
$app['aggregation.timeblocks'] = function ($app) {
    return new App\Aggregation\TimeBlocks(
        $app['repository.devicelog'],
        $app['aggregation.devicelogs'],
        $app['elasticsearch']
    );
};
$app['aggregation.devicelogs'] = function ($app) {
    return new App\Aggregation\DeviceLogs;
};

// Command
$app['command.scannercommand'] = function ($app) {
    return new \App\Command\ScannerCommand(
        $app['repository.devicelog'],
        new App\Command\FailureLimiter(),
        $app['aggregation.timeblocks'],
        $app['scan'],
        $app['scan.options']
    );
};

// Lookup
$app['lookup.macaddress'] = function ($app) {
    return new App\Lookup\MacAddress($app['scan.options']['macAddressApiKey']);
};

// Repository
$app['repository.devicelog'] = function ($app) {
    return $app['em']->getRepository('\App\Entity\DeviceLog');
};
$app['repository.timeblocks'] = function ($app) {
    return new \App\Repository\TimeBlocksRepository($app['elasticsearch']);
};
$app['repository.device'] = function ($app) {
    return $app['em']->getRepository('\App\Entity\Device');
};

// Scan
$app['scan'] = function ($app) {
    return new App\Scan(
        $app['em'],
        new App\Scan\Tool\Nmap(),
        $app['lookup.macaddress'],
        $app['scan.options']
    );
};

return $app;
