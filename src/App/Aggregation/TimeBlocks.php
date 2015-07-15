<?php
namespace App\Aggregation;

use Elastica\Client;
use App\Aggregation\DeviceLogs as DeviceLogAggregator;
use App\Repository\DeviceLogRepository;
use Elastica\Document;

class TimeBlocks
{
    private $devicelogRepository;
    private $aggregator;
    private $elasticsearch;

    /**
     * Constructor
     *
     * @param DeviceLogRepository $devicelogRepository
     * @param DeviceLogAggregator $aggregator
     * @param Client $elasticsearch
     */
    public function __construct(
        DeviceLogRepository $devicelogRepository,
        DeviceLogAggregator $aggregator,
        Client $elasticsearch
    ) {
        $this->devicelogRepository = $devicelogRepository;
        $this->aggregator = $aggregator;
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
        $now = new \DateTime('now');
        $deviceLogs = $this->devicelogRepository->findByDay($now);
        $rows = $this->aggregator->aggregate($deviceLogs, $offlineGap);
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
                $documents[] = new Document(
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
