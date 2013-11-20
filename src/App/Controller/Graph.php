<?php
namespace App\Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;
use App\Aggregation\DeviceLogs as DeviceLogAggregator;


class Graph implements ControllerProviderInterface
{
    private $app;

    public function connect(Application $app)
    {
        $this->app = $app;
        $controllers = $app['controllers_factory'];

        $controllers->get('/{date}', array($this, 'indexAction'))->value('date', date('Y-m-d'));
        $controllers->get('/old/{date}', array($this, 'oldAction'))->value('date', 'now');
        return $controllers;
    }

    public function oldAction($date)
    {
        $date = new \DateTime($date);
        $deviceLogs = $this->app['em']->getRepository('\App\Entity\DeviceLog')->findByDay($date);
        $aggregator = new DeviceLogAggregator();
        $rows = $aggregator->aggregate($deviceLogs, $this->app['scan.options']['offlineGap']);

        return $this->app['twig']->render(
            'graph/index.twig',
            array(
                'rows' => $rows
            )
        );
    }

    public function indexAction($date)
    {
        $date = new \DateTime($date);
        $enddate = clone $date;
        $enddate->add(new \DateInterval('P1D'));

        $elasticaIndex = $this->app['es']->getIndex('devices');

        $rangeFilter = new \Elastica\Filter\Range();
        $rangeFilter->addField('start', array(
            'from' => $date->format('c'),
            'to' => $enddate->format('c'))
        );

        $dateFacet = new \Elastica\Facet\DateHistogram('dateFacet');
        $dateFacet->setField('start');
        $dateFacet->setInterval('day');
        $dateFacet->setGlobal(true);

        // Create the actual search object with some data.
        $elasticaQuery = new \Elastica\Query();
        $elasticaQuery->setFilter($rangeFilter);
        $elasticaQuery->setSort(array('device' => array('order' => 'asc')));
        $elasticaQuery->setLimit(10000);
        $elasticaQuery->addFacet($dateFacet);

        //Search on the index.
        $elasticaResultSet = $elasticaIndex->search($elasticaQuery);
        $elasticaFacets = $elasticaResultSet->getFacets();
        $dates = array();
        foreach ($elasticaFacets['dateFacet']['entries'] as $elasticaFacet) {
            $row['date'] =  substr($elasticaFacet['time'], 0, -3);
            $row['count'] = $elasticaFacet['count'];
            $dates[] = $row;
        }
        $rows = array();
        foreach ($elasticaResultSet as $elasticaResult) {
            $row = $elasticaResult->getData();
            $rows[] = $row;
        }

        return $this->app['twig']->render(
            'graph/search.twig',
            array(
                'rows' => $rows,
                'dates' => $dates,
            )
        );
    }
}
