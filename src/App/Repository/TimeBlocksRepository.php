<?php

namespace App\Repository;

use Elastica\Client;
use Elastica\Filter\Range;
use Elastica\Query;
use Elastica\Facet\DateHistogram;

/**
 * TimeBlocksRepository
 */
class TimeBlocksRepository
{
    /**
     * Elastica client
     *
     * @var Client
     */
    private $client;

    /**
     * Constructor
     *
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Fetch results by date range
     *
     * @param \DateTime $start
     * @param \DateTime $end
     * @return array
     */
    public function fetchByRange(\DateTime $start, \DateTime $end)
    {
        $elasticaIndex = $this->client->getIndex('devices');

        $dateFacet = new DateHistogram('dateFacet');
        $dateFacet->setField('start');
        $dateFacet->setInterval('day');
        $dateFacet->setGlobal(true);

        $rangeFilter = new Range();
        $rangeFilter->addField(
            'start',
            array(
                'from' => $start->format('c'),
                'to' => $end->format('c')
            )
        );

        // Create the actual search object with some data.
        $elasticaQuery = new Query();
        $elasticaQuery->setPostFilter($rangeFilter);
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
        return array('dates' => $dates, 'rows' => $rows);
    }
}
