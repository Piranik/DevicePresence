<?php
namespace App\Scan\Tool;

use Symfony\Component\Process\Process;

/**
 * Arp
 *
 * @author Tim de Pater <code@trafex.nl>
 */
class Arp
{
    const ARP_COMMAND = 'arp';

    /**
     * ARP table
     *
     * @var string
     */
    private $arpTable;

    /**
     * Get the MAC address for the given IP
     *
     * @param string $ip
     * @return null|string
     */
    public function getMacAddressForIp($ip)
    {
        $arpTable = $this->getArpTable();

        foreach (explode("\n", $arpTable) as $line) {
            if (false !== strpos($line, $ip)) {
                $macAddress = substr($line, 33, 17);
                if (strpos($macAddress, 'incomplete') !== false) {
                    return null;
                }
                return $macAddress;
            }
        }
        return null;
    }

    /**
     * Fetch the current ARP table
     *
     * @return string
     */
    public function getArpTable()
    {
        if (null === $this->arpTable) {
            $cmd = sprintf(
                '%s -n',
                self::ARP_COMMAND
            );
            $process = new Process($cmd);
            $process->run();

            if ($process->getExitCode() > 1) {
                throw new \RuntimeException($process->getErrorOutput());
            }
            $this->arpTable = $process->getOutput();
        }
        return $this->arpTable;
    }
}
