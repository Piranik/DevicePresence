<?php
namespace App\Scan\Tool\Nmap;

use Symfony\Component\Process\Process;

class Program
{
    const COMMAND = 'nmap';

    /**
     * Run the nmap command
     *
     * @param string $network
     * @return string
     */
    public function nmap($network)
    {
        $cmd = sprintf(
            '%s -oX - -sn %s',
            self::COMMAND,
            $network
        );
        $process = new Process($cmd);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }
        return $process->getOutput();
    }
}
