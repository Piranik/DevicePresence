<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TimeBlocks
 */
class TimeBlocks
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $start;

    /**
     * @var \DateTime
     */
    private $end;

    /**
     * @var string
     */
    private $ip;

    /**
     * @var \App\Entity\Device
     */
    private $device;


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
     * Set start
     *
     * @param \DateTime $start
     * @return TimeBlocks
     */
    public function setStart($start)
    {
        $this->start = $start;
    
        return $this;
    }

    /**
     * Get start
     *
     * @return \DateTime 
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * Set end
     *
     * @param \DateTime $end
     * @return TimeBlocks
     */
    public function setEnd($end)
    {
        $this->end = $end;
    
        return $this;
    }

    /**
     * Get end
     *
     * @return \DateTime 
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * Set ip
     *
     * @param string $ip
     * @return TimeBlocks
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
    
        return $this;
    }

    /**
     * Get ip
     *
     * @return string 
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set device
     *
     * @param \App\Entity\Device $device
     * @return TimeBlocks
     */
    public function setDevice(\App\Entity\Device $device = null)
    {
        $this->device = $device;
    
        return $this;
    }

    /**
     * Get device
     *
     * @return \App\Entity\Device 
     */
    public function getDevice()
    {
        return $this->device;
    }
}
