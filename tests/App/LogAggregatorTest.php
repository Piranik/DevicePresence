<?php
namespace App;

use App\LogAggregator;
use App\Entity\Device;
use App\Entity\DeviceLog;

class LogAggregatorTest extends \PHPUnit_Framework_TestCase
{
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
        $this->assertEquals(new \DateTime('2013-01-01 12:10'), $result['00:00:00:00:00:01'][1]['start']);
        $this->assertEquals(new \DateTime('2013-01-01 12:12'), $result['00:00:00:00:00:01'][1]['end']);
        $this->assertEquals(new \DateTime('2013-01-01 14:30'), $result['00:00:00:00:00:01'][2]['start']);
        $this->assertEquals(new \DateTime('2013-01-01 14:30'), $result['00:00:00:00:00:01'][2]['end']);

        $this->assertCount(1, $result['00:00:00:00:00:02']);
        $this->assertEquals(new \DateTime('2013-01-01 12:00'), $result['00:00:00:00:00:02'][0]['start']);
        $this->assertEquals(new \DateTime('2013-01-01 12:03'), $result['00:00:00:00:00:02'][0]['end']);
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
