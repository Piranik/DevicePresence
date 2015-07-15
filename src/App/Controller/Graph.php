<?php
namespace App\Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;

class Graph implements ControllerProviderInterface
{
    /**
     * Application
     *
     * @var Application
     */
    private $app;

    /**
     * Define the routes this controller uses
     *
     * @param Application $app
     * @return \Silex\ControllerCollection
     */
    public function connect(Application $app)
    {
        $this->app = $app;
        $controllers = $app['controllers_factory'];

        $controllers->get('/{date}', array($this, 'indexAction'))->value('date', date('Y-m-d'));
        return $controllers;
    }

    /**
     * Retrieve the timeblock from ElasticSearch
     *
     * @param string $date
     */
    public function indexAction($date)
    {
        $date = new \DateTime($date);
        $enddate = clone $date;
        $enddate->add(new \DateInterval('P1D'));

        $result = $this->app['repository.timeblocks']->fetchByRange($date, $enddate);

        return $this->app['twig']->render(
            'graph/search.twig',
            $result
        );
    }
}
