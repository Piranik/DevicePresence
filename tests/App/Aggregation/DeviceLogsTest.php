<?php
namespace App\Aggregation;

use App\Aggregation\DeviceLogs as LogAggregator;
use App\Entity\Device;
use App\Entity\DeviceLog;

/**
 * DeviceLogsTest
 *
 * @coversDefaultClass \App\Aggregation\DeviceLogs
 * @see \PHPUnit_Framework_TestCase
 * @author Tim de Pater <code@trafex.nl>
 */
class DeviceLogsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::aggregate
     */
    public function testOfflineGapDifference()
    {
        $deviceLogs = array();

        $device = new Device();
        $device->setMacAddress('00:00:00:00:00:01');
        $device->setVendor('TrafeX inc.');

        $deviceLogs[] = $this->getDeviceLogForDate(new \DateTime('2013-01-01 12:00'), $device);
        $deviceLogs[] = $this->getDeviceLogForDate(new \DateTime('2013-01-01 12:05'), $device);

        $aggregator = new LogAggregator();
        $result = $aggregator->aggregate($deviceLogs, 300);
        $this->assertCount(1, $result);
        $this->assertCount(1, $result['00:00:00:00:00:01']);

        $result = $aggregator->aggregate($deviceLogs, 299);
        $this->assertCount(1, $result);
        $this->assertCount(2, $result['00:00:00:00:00:01']);
    }

    /**
     * @covers ::aggregate
     */
    public function testTimeAggregation()
    {
        $deviceLogs = array();

        $device = new Device();
        $device->setMacAddress('00:00:00:00:00:01');
        $device->setVendor('TrafeX inc.');

        $deviceLogs[] = $this->getDeviceLogForDate(new \DateTime('2013-01-01 12:00'), $device);
        $deviceLogs[] = $this->getDeviceLogForDate(new \DateTime('2013-01-01 12:10'), $device);
        $deviceLogs[] = $this->getDeviceLogForDate(new \DateTime('2013-01-01 12:12'), $device);
        $deviceLogs[] = $this->getDeviceLogForDate(new \DateTime('2013-01-01 14:30'), $device);

        $device = new Device();
        $device->setMacAddress('00:00:00:00:00:02');
        $device->setVendor('TrafeX inc.');

        $deviceLogs[] = $this->getDeviceLogForDate(new \DateTime('2013-01-01 12:00'), $device);
        $deviceLogs[] = $this->getDeviceLogForDate(new \DateTime('2013-01-01 12:03'), $device);

        $aggregator = new LogAggregator();
        $result = $aggregator->aggregate($deviceLogs, 300);
        $this->assertCount(2, $result);
        $this->assertCount(3, $result['00:00:00:00:00:01']);
        $this->assertEquals(new \DateTime('2013-01-01 12:00'), $result['00:00:00:00:00:01'][0]['start']);
        $this->assertEquals(new \DateTime('2013-01-01 12:00'), $result['00:00:00:00:00:01'][0]['end']);
        $this->assertNotNull($result['00:00:00:00:00:01'][0]['device']);
        $this->assertNotNull($result['00:00:00:00:00:01'][0]['ip']);
        $this->assertEquals(new \DateTime('2013-01-01 12:10'), $result['00:00:00:00:00:01'][1]['start']);
        $this->assertEquals(new \DateTime('2013-01-01 12:12'), $result['00:00:00:00:00:01'][1]['end']);
        $this->assertNotNull($result['00:00:00:00:00:01'][1]['device']);
        $this->assertNotNull($result['00:00:00:00:00:01'][1]['ip']);
        $this->assertEquals(new \DateTime('2013-01-01 14:30'), $result['00:00:00:00:00:01'][2]['start']);
        $this->assertEquals(new \DateTime('2013-01-01 14:30'), $result['00:00:00:00:00:01'][2]['end']);
        $this->assertNotNull($result['00:00:00:00:00:01'][2]['device']);
        $this->assertNotNull($result['00:00:00:00:00:01'][2]['ip']);

        $this->assertCount(1, $result['00:00:00:00:00:02']);
        $this->assertEquals(new \DateTime('2013-01-01 12:00'), $result['00:00:00:00:00:02'][0]['start']);
        $this->assertEquals(new \DateTime('2013-01-01 12:03'), $result['00:00:00:00:00:02'][0]['end']);
        $this->assertNotNull($result['00:00:00:00:00:02'][0]['device']);
        $this->assertNotNull($result['00:00:00:00:00:02'][0]['ip']);
    }

    /**
     * @covers ::aggregate
     */
    public function testNoDeviceLogs()
    {
        $aggregator = new LogAggregator();
        $this->assertEquals(array(), $aggregator->aggregate(array(), 300));
    }

    private function getDeviceLogForDate(\DateTime $date, Device $device)
    {
        $deviceLog = new DeviceLog();
        $deviceLog->setIp('192.168.0.1');
        $deviceLog->setDate($date);
        $deviceLog->setDevice($device);
        return $deviceLog;
    }
}
