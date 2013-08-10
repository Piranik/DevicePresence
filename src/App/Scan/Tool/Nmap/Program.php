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
     * @param string $interface
     * @return string
     */
    public function nmap($network, $interface = null)
    {
        $interfaceOption = null;
        if (null !== $interface) {
            $interfaceOption = '-e ' . $interface;
        }
        $cmd = sprintf(
            '%s %s -oX - -sn %s',
            self::COMMAND,
            $interfaceOption,
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
