<?php
namespace App\Entity;

class Device
{

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $macaddress;

    /**
     * @var string
     */
    private $lastip;

    /**
     * @var \DateTime
     */
    private $firstseen;

    /**
     * @var \DateTime
     */
    private $lastseen;

    /**
     * @var \DateTime
     */
    private $updated;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set macaddress
     *
     * @param string $macaddress
     * @return Device
     */
    public function setMacaddress($macaddress)
    {
        $this->macaddress = $macaddress;
    
        return $this;
    }

    /**
     * Get macaddress
     *
     * @return string 
     */
    public function getMacaddress()
    {
        return $this->macaddress;
    }

    /**
     * Set lastip
     *
     * @param string $lastip
     * @return Device
     */
    public function setLastip($lastip)
    {
        $this->lastip = $lastip;
    
        return $this;
    }

    /**
     * Get lastip
     *
     * @return string 
     */
    public function getLastip()
    {
        return $this->lastip;
    }

    /**
     * Set firstseen
     *
     * @param \DateTime $firstseen
     * @return Device
     */
    public function setFirstseen($firstseen)
    {
        $this->firstseen = $firstseen;
    
        return $this;
    }

    /**
     * Get firstseen
     *
     * @return \DateTime 
     */
    public function getFirstseen()
    {
        return $this->firstseen;
    }

    /**
     * Set lastseen
     *
     * @param \DateTime $lastseen
     * @return Device
     */
    public function setLastseen($lastseen)
    {
        $this->lastseen = $lastseen;
    
        return $this;
    }

    /**
     * Get lastseen
     *
     * @return \DateTime 
     */
    public function getLastseen()
    {
        return $this->lastseen;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     * @return Device
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    
        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime 
     */
    public function getUpdated()
    {
        return $this->updated;
    }
    /**
     * @var string
     */
    private $vendor;


    /**
     * Set vendor
     *
     * @param string $vendor
     * @return Device
     */
    public function setVendor($vendor)
    {
        $this->vendor = $vendor;
    
        return $this;
    }

    /**
     * Get vendor
     *
     * @return string 
     */
    public function getVendor()
    {
        return $this->vendor;
    }
}