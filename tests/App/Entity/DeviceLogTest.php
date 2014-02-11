<?php

namespace App\Entity;

/**
 * DeviceLogTest
 *
 * @see \PHPUnit_Framework_TestCase
 * @author Tim de Pater <code@trafex.nl>
 */
class DeviceLogTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \App\Entity\DeviceLog
     */
    public function testGetterSetters()
    {
        $entity = new DeviceLog();
        $entity->setDate(new \DateTime('now'));
        $entity->setIp('127.0.0.1');
        $entity->setDevice(new Device());

        $this->assertNull($entity->getId());
        $this->assertEquals(new \DateTime('now'), $entity->getDate());
        $this->assertEquals('127.0.0.1', $entity->getIp());
        $this->assertEquals(new Device(), $entity->getDevice());
    }
}
