<?php
namespace App\Scan;

class Host
{
    private $ip;
    private $rtt;
    private $alive;

    /**
     * Get ip.
     *
     * @return ip.
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set ip.
     *
     * @param ip the value to set.
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
    }

    /**
     * Get rtt.
     *
     * @return rtt.
     */
    public function getRtt()
    {
        return $this->rtt;
    }

    /**
     * Set rtt.
     *
     * @param rtt the value to set.
     */
    public function setRtt($rtt)
    {
        $this->rtt = $rtt;
    }

    /**
     * Get alive.
     *
     * @return alive.
     */
    public function getAlive()
    {
        return $this->alive;
    }

    /**
     * Set alive.
     *
     * @param alive the value to set.
     */
    public function setAlive($alive)
    {
        $this->alive = $alive;
    }
}
