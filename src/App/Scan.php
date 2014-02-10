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
     * The tool to use for scanning
     *
     * @var Nmap
     */
    private $scanTool;

    /**
     * lookup
     *
     * @var mixed
     */
    private $lookup;

    /**
     * Constructor
     *
     * @param EntityManager $em
     * @param Nmap $scanTool
     * @param MacAddress $lookup
     * @param array $config
     */
    public function __construct(
        EntityManager $em,
        Nmap $scanTool,
        MacAddress $lookup,
        array $config
    ) {
        $this->entityManager = $em;
        $this->scanTool = $scanTool;
        $this->lookup = $lookup;
        $this->config = $config;
    }

    /**
     * Set the output to write to
     *
     * @param OutputInterface $output
     */
    public function setOutput(OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * Scan using nmap and save the results
     *
     * @return int
     */
    public function scanUsingNmap()
    {
        $results = $this->scanTool->pingNetwork($this->config['network'], $this->config['interface']);
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
                    $vendor = $this->lookup->getVendorForMacAddress($macAddress);
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
