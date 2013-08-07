<?php
namespace App\Scan\Tool;

use Symfony\Component\Process\Process;
use App\Scan\Tool\Fping\Mapper;
use App\Scan\Host;

/**
 * Fping wrapper
 *
 * @author Tim de Pater <code@trafex.nl>
 */
class Fping
{
    const PING_COMMAND = 'fping';

    /**
     * Ping a network
     *
     * @param string $network
     * @return array
     */
    public function pingNetwork($network)
    {
        $mapper = new Mapper();
        $result = $this->runPing($network);

        $results = array();
        foreach (explode("\n", $result) as $line) {
            if (empty($line)) {
                continue;
            }
            $results[] = $mapper->toEntity($line, new Host());
        }
        return $results;
    }

    /**
     * Run the ping command
     *
     * @param string $network
     * @return string
     */
    private function runPing($network)
    {
        $cmd = sprintf(
            '%s -g -c 1 %s',
            self::PING_COMMAND,
            $network
        );
        $process = new Process($cmd);
        $process->run();

        if ($process->getExitCode() > 1) {
            throw new \RuntimeException($process->getErrorOutput());
        }
        return $process->getOutput();
    }
}
