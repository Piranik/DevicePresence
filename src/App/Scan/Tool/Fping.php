<?php
namespace App\Scan\Tool;

use Symfony\Component\Process\Process;
use App\Scan\Tool\Fping\Mapper;
use App\Scan\Host;

class Fping
{
    const PING_COMMAND = 'fping';

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

    public function scan()
    {
        $results = $this->runPing();
    }

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
        // @todo: fping -c 1 -g 192.168.20.0/24 && arp -n | grep -i
        // "00:15:AD:FF:81:93"
        return $process->getOutput();
    }

}
