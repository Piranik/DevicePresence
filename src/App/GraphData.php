<?php
namespace App;

use App\Entity\DeviceLog;
use App\Entity\Device;
use Doctrine\ORM\EntityManager;

/**
 * Generates the data for the timeline graph
 *
 * @author Tim de Pater <code@trafex.nl>
 */
class GraphData
{
    private $entityManager;
    private $config;

    public function __construct(EntityManager $entityManager, array $config)
    {
        $this->entityManager = $entityManager;
        $this->config = $config;
    }

    public function getData()
    {
        // @todo: Add datepicker to choose the day
        $date = new \DateTime('now');
        $query = $this->entityManager->createQuery(
            'select dl, d from \App\Entity\DeviceLog dl join dl.device d where dl.date between :datestart and :dateend order by d.id asc, dl.date asc'
        );
        $query->setParameters(
            array(
                'datestart' => $date->format('Y-m-d'),
                'dateend' => $date->add(new \DateInterval('P1D'))->format('Y-m-d'),
            )
        );
        $deviceLogs = $query->getResult();

        $results = array();
        $lastDate = array();
        $curDevice = null;
        $i = null;
        foreach ($deviceLogs as $deviceLog) {
            if ($curDevice !== $deviceLog->getDevice()->getId()) {

                if (null !== $i && !isset($results[$curDevice][$i]['end'])) {
                    $results[$curDevice][$i]['end'] = $lastDate;
                }

                $curDevice = $deviceLog->getDevice()->getId();
                $lastDate = $deviceLog->getDate();
                $i = 0;
                $results[$curDevice][$i] = array();
            }
            $results[$curDevice][$i]['device'] = sprintf(
                '%s (%s)',
                $deviceLog->getDevice()->getMacAddress(),
                $deviceLog->getDevice()->getVendor()
            );
            if (!isset($results[$curDevice][$i]['start'])) {
                $results[$curDevice][$i]['start'] = $deviceLog->getDate();
            }

            $timeDiff = strtotime($deviceLog->getDate()->format('Y-m-d H:i:s'))
                - strtotime($lastDate->format('Y-m-d H:i:s'));

            if ($timeDiff > $this->config['offlineGap']) {
                $results[$curDevice][$i]['end'] = $deviceLog->getDate();
                $i++;
            }
            $lastDate = $deviceLog->getDate();
        }
        return $results;
    }
}
