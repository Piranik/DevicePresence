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
        $data = $aggregator->aggregate($deviceLogs, $this->app['timeline.options']['offlineGap']);

        $rows = '';
        foreach ($data as $device) {
            foreach ($device as $row) {
                if (isset($row['device'], $row['start'], $row['end'])) {
                    $rows .= sprintf(
                        "[ '%s', new Date(%s), new Date(%s) ],\n",
                        $row['device'],
                        $row['start']->format('Y, n, j, H, i, s'),
                        $row['end']->format('Y, n, j, H, i, s')
                    );
                }
            }
        }
        $rows = rtrim($rows, ',');

        return $this->app['twig']->render(
            'graph/index.twig',
            array(
                'rows' => $rows
            )
        );
    }
}
