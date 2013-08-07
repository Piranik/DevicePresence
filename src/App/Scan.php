<?php
namespace App;

use App\Scan\Tool\Fping;
use App\Scan\Tool\Arp;
use App\Lookup\MacAddress;
use App\Entity\Device;
use App\Entity\DeviceLog;
use Doctrine\ORM\EntityManager;

/**
 * Scan the network
 *
 * @author Tim de Pater <code@trafex.nl>
 */
class Scan
{
    /**
     * Entity manager
     *
     * @var EntityManager
     */
    private $entityManager;

    /**
     * Scan config
     *
     * @var array
     */
    private $config;

    /**
     * Constructor
     *
     * @param EntityManager $em
     * @param array $config
     */
    public function __construct(EntityManager $em, array $config)
    {
        $this->entityManager = $em;
        $this->config = $config;
    }

    /**
     * Scan and save the results
     *
     * @return array
     */
    public function scan()
    {
        $arp = new Arp();
        $lookup = new MacAddress($this->config['macAddressApiKey']);
        $tool = new Fping();
        $results = $tool->pingNetwork($this->config['network']);
        foreach ($results as $result) {
            $repo = $this->entityManager->getRepository('\App\Entity\Device');
            $macAddress = $arp->getMacAddressForIp($result->getIp());
            if (null === $macAddress) {
                // Skip
                continue;
            }
            $device = $repo->findOneBy(array('macaddress' => $macAddress));
            if (null === $device) {
                $device = new Device();
                $device->setMacAddress($macAddress);
                $device->setFirstSeen(new \DateTime('now'));
                $device->setVendor($lookup->getVendorForMacAddress($macAddress));
            }

            $device->setLastIp($result->getIp());
            $device->setLastSeen(new \DateTime('now'));
            $device->setUpdated(new \DateTime('now'));
            $this->entityManager->persist($device);

            $deviceLog = new DeviceLog();
            $deviceLog->setIp($result->getIp());
            $deviceLog->setDate(new \DateTime('now'));
            $deviceLog->setDevice($device);
            $this->entityManager->persist($deviceLog);
        }

        $this->entityManager->flush();
        $this->entityManager->clear();

        return $results;
    }
}
