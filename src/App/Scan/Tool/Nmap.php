<?php
namespace App\Scan\Tool;

use Symfony\Component\Process\Process;
use App\Scan\Host;
use App\Scan\Tool\Nmap\Mapper;

/**
 * Fping wrapper
 *
 * @author Tim de Pater <code@trafex.nl>
 */
class Nmap
{
    const COMMAND = 'nmap';

    /**
     * Ping a network
     *
     * @param string $network
     * @return array
     */
    public function pingNetwork($network)
    {
        $mapper = new Mapper();
        $result = $this->runNmap($network);
        $xpath = $this->getXpath($result);

        $results = array();
        $hosts = $xpath->query('//host');
        foreach ($hosts as $host) {
            $results[] = $mapper->toEntity($xpath, $host, new Host());
        }

        return $results;
    }

    /**
     * Run the nmap command
     *
     * @param string $network
     * @return string
     */
    private function runNmap($network)
    {
        $cmd = sprintf(
            '%s -oX - -sn %s',
            self::COMMAND,
            $network
        );
        $process = new Process($cmd);
        $process->run();

        if ($process->getExitCode() > 1) {
            throw new \RuntimeException($process->getErrorOutput());
        }
        return $process->getOutput();
    }

    /**
     * Get a \DOMXpath object for the XML
     *
     * @param string $xml
     * @return \DOMXpath
     */
    private function getXpath($xml)
    {
        $domDoc = new \DOMDocument();
        $dom = $domDoc->loadXML($xml);
        if (false === $dom) {
            new \RuntimeException(
                sprintf('Couldn\'t load the XML from nmap: %s', $input)
            );
        }
        return new \DOMXpath($domDoc);
    }
}

