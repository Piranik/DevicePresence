<?php
namespace App\Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;

class Devices implements ControllerProviderInterface
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
     */
    public function connect(Application $app)
    {
        $this->app = $app;
        $controllers = $app['controllers_factory'];

        $controllers->get('/', array($this, 'indexAction'));
        return $controllers;
    }

    /**
     * Retrieve the devices
     *
     */
    public function indexAction()
    {
        $result = $this->app['repository.device']->findAll();

        return $this->app['twig']->render(
            'devices/index.twig',
            array('devices' => $result)
        );
    }
}
