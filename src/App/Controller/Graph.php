<?php
namespace App\Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;
use App\LogAggregator;


class Graph implements ControllerProviderInterface
{
    private $app;

    public function connect(Application $app)
    {
        $this->app = $app;
        $controllers = $app['controllers_factory'];

        $controllers->get('/{date}', array($this, 'indexAction'))->value('date', 'now');
        return $controllers;
    }

    public function indexAction($date)
    {
        $date = new \DateTime($date);
        $deviceLogs = $this->app['em']->getRepository('\App\Entity\DeviceLog')->findByDay($date);
        $aggregator = new LogAggregator();
        $rows = $aggregator->aggregate($deviceLogs, $this->app['timeline.options']['offlineGap']);

        return $this->app['twig']->render(
            'graph/index.twig',
            array(
                'rows' => $rows
            )
        );
    }
}
