<?php
namespace App;

use App\Scan\Tool\Nmap;
use App\Lookup\MacAddress;
use App\Entity\Device;
use App\Entity\DeviceLog;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Output\OutputInterface;

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
     * Output
     *
     * @var OutputInterface
     */
    private $output;

    /**
     * Constructor
     *
     * @param EntityManager $em
     * @param OutputInterface $output
     * @param array $config
     */
    public function __construct(EntityManager $em, OutputInterface $output, array $config)
    {
        $this->entityManager = $em;
        $this->output = $output;
        $this->config = $config;
    }

    /**
     * Scan using nmap and save the results
     *
     * @return int
     */
    public function scanUsingNmap()
    {
        $tool = new Nmap();
        $lookup = new MacAddress($this->config['macAddressApiKey']);
        $results = $tool->pingNetwork($this->config['network'], $this->config['interface']);
        $this->output->writeLn(sprintf('Found %u online devices', count($results)));

        $updatedCnt = 0;
        foreach ($results as $result) {
            $repo = $this->entityManager->getRepository('\App\Entity\Device');
            $macAddress = $result->getMacAddress();
            if (null === $macAddress) {
                // Skip
                continue;
            }
            $device = $repo->findOneBy(array('macaddress' => $macAddress));
            if (null === $device) {
                $device = new Device();
                $device->setMacAddress($macAddress);
                $device->setFirstSeen(new \DateTime('now'));

                $vendor = $result->getVendor();
                if (null === $vendor) {
                    // Try to lookup
                    $vendor = $lookup->getVendorForMacAddress($macAddress);
                }
                $device->setVendor($vendor);
                $this->output->writeLn(sprintf('Found a new device: %s (%s)', $macAddress, $vendor));
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

            $this->entityManager->flush();
            $this->entityManager->clear();
            $updatedCnt++;
        }
        return $updatedCnt;
    }
}
