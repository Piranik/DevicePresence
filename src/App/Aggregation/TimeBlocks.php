<?php
namespace App\Aggregation;

use Doctrine\ORM\EntityManager;
use Elastica\Client;
use App\Aggregation\DeviceLogs as DeviceLogAggregator;

class TimeBlocks
{
    private $em;
    private $elasticsearch;

    public function __construct(EntityManager $em, Client $elasticsearch)
    {
        $this->em = $em;
        $this->elasticsearch = $elasticsearch;
    }

    /**
     * Fetch all devicelogs of today and index them as timeblocks in
     * elasticsearch
     *
     * @param integer $offlineGap
     * @return integer
     */
    public function aggregateToTimeBlocks($offlineGap)
    {
        // @todo: Delete everything older then today

        $now = new \DateTime('now');
        $deviceLogs = $this->em->getRepository('\App\Entity\DeviceLog')->findByDay($now);
        $aggregator = new DeviceLogAggregator();
        $rows = $aggregator->aggregate($deviceLogs, $offlineGap);
        if (0 === count($rows)) {
            // Nothing to do
            return 0;
        }

        $elasticaIndex = $this->elasticsearch->getIndex('devices');
        $elasticaType = $elasticaIndex->getType('timeblock');

        $documents = array();
        foreach ($rows as $row) {
            foreach ($row as $log) {

                $log['start'] = $log['start']->format('c');
                $log['end'] = $log['end']->format('c');
                // @todo: Check if exists
                $documents[] = new \Elastica\Document(
                    md5($log['device'] . $log['start']),
                    $log
                );
            }
        }

        $elasticaType->addDocuments($documents);
        $elasticaType->getIndex()->refresh();
        return count($documents);
    }
}
