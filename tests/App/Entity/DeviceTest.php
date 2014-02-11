<?php

namespace App\Entity;

/**
 * DeviceTest
 *
 * @see \PHPUnit_Framework_TestCase
 * @author Tim de Pater <code@trafex.nl>
 */
class DeviceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \App\Entity\Device
     */
    public function testGetterSetters()
    {
        $entity = new Device();
        $entity->setMacaddress('00:00:00:00:00');
        $entity->setLastip('127.0.0.1');
        $entity->setFirstseen(new \DateTime('now'));
        $entity->setLastseen(new \DateTime('now'));
        $entity->setUpdated(new \DateTime('now'));
        $entity->setVendor('TrafeX inc');
        $entity->addDevicelog(new DeviceLog());
        $entity->removeDevicelog(new DeviceLog());

        $this->assertNull($entity->getId());
        $this->assertEquals('127.0.0.1', $entity->getLastIp());
        $this->assertEquals('00:00:00:00:00', $entity->getMacaddress());
        $this->assertEquals(new \DateTime('now'), $entity->getFirstseen());
        $this->assertEquals(new \DateTime('now'), $entity->getLastseen());
        $this->assertEquals(new \DateTime('now'), $entity->getUpdated());
        $this->assertEquals('TrafeX inc', $entity->getVendor());
        $this->assertInstanceOf('\Doctrine\Common\Collections\ArrayCollection', $entity->getDeviceLog());
    }
}
