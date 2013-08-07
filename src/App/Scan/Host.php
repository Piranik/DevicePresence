<?php
namespace App\Scan;

class Host
{
    private $ip;
    private $alive;
    private $macaddress;
    private $vendor;

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

    /**
     * Get macaddress.
     *
     * @return macaddress.
     */
    public function getMacaddress()
    {
        return $this->macaddress;
    }

    /**
     * Set macaddress.
     *
     * @param macaddress the value to set.
     */
    public function setMacaddress($macaddress)
    {
        $this->macaddress = $macaddress;
    }

    /**
     * Get vendor.
     *
     * @return vendor.
     */
    public function getVendor()
    {
        return $this->vendor;
    }

    /**
     * Set vendor.
     *
     * @param vendor the value to set.
     */
    public function setVendor($vendor)
    {
        $this->vendor = $vendor;
    }
}
