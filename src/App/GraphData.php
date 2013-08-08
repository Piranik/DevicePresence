<?php
namespace App;

use App\Entity\DeviceLog;
use App\Entity\Device;
use Doctrine\ORM\EntityManager;

class GraphData
{
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getData()
    {
        $query = $this->entityManager->createQuery(
            'select dl, d from \App\Entity\DeviceLog dl join dl.device d order by d.id asc, dl.date asc'
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

            if ($timeDiff > 600) {
                $results[$curDevice][$i]['end'] = $deviceLog->getDate();
                $i++;
            }
            $lastDate = $deviceLog->getDate();
        }
        return $results;
    }
}
