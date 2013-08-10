<?php
namespace App\Scan\Tool;

use App\Scan\Host;
use App\Scan\Tool\Nmap\Mapper;
use App\Scan\Tool\Nmap\Program;

/**
 * Nmap wrapper
 *
 * @author Tim de Pater <code@trafex.nl>
 */
class Nmap
{
    /**
     * Program
     *
     * @var Program;
     */
    private $program;

    /**
     * Ping a network
     *
     * @param string $network
     * @param string $interface
     * @return array
     */
    public function pingNetwork($network, $interface = null)
    {
        $result = $this->getProgram()->nmap($network, $interface);
        $xpath = $this->getXpath($result);

        $mapper = new Mapper();
        $results = array();
        $hosts = $xpath->query('//host');
        foreach ($hosts as $host) {
            $results[] = $mapper->toEntity($xpath, $host, new Host());
        }

        return $results;
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
                sprintf('Couldn\'t load the XML from nmap: %s', $xml)
            );
        }
        return new \DOMXpath($domDoc);
    }

    /**
     * Get program.
     *
     * @return Program
     */
    public function getProgram()
    {
        if (null === $this->program) {
            $this->program = new Program();
        }
        return $this->program;
    }

    /**
     * Set program.
     *
     * @param Program $program
     */
    public function setProgram(Program $program)
    {
        $this->program = $program;
    }
}
