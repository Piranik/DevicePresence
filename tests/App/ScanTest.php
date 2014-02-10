<?php
namespace App;

use App\Scan\Host;
use App\Entity\Device;
use App\Entity\DeviceLog;

/**
 * ScanTest
 *
 * @coversDefaultClass \App\Scan
 * @see \PHPUnit_Framework_TestCase
 * @author Tim de Pater <code@trafex.nl>
 */
class ScanTest extends \PHPUnit_Framework_TestCase
{
    private $dummyConfig = array(
        'network' => '127.0.0.1/24',
        'interface' => 'eth0',
    );

    public function tearDown()
    {
        \Mockery::close();
    }

    /**
     * @covers ::__construct
     * @covers ::setOutput
     * @covers ::scanUsingNmap
     */
    public function testEntitiesGetCreated()
    {
        $device = new Device();
        $entityManager = $this->createEntityManagerMock($device);

        $host = new Host();
        $host->setIp('127.0.0.1');
        $host->setMacAddress('00:00:00:00:00:83');

        $scanTool = \Mockery::mock('\App\Scan\Tool\Nmap');
        $scanTool->shouldReceive('pingNetwork')
            ->once()
            ->withArgs(array($this->dummyConfig['network'], $this->dummyConfig['interface']))
            ->andReturn(
                array(
                    $host,
                    clone $host,
                )
            );

        $lookup = \Mockery::mock('\App\Lookup\MacAddress');

        $scan = new Scan(
            $entityManager,
            $scanTool,
            $lookup,
            $this->dummyConfig
        );
        $scan->setOutput($this->createOutputMock());

        $this->assertEquals(2, $scan->scanUsingNmap());
    }

    /**
     * @covers ::__construct
     * @covers ::setOutput
     * @covers ::scanUsingNmap
     */
    public function testNewDeviceAdded()
    {
        $device = null;
        $entityManager = $this->createEntityManagerMock($device);

        $host = new Host();
        $host->setIp('127.0.0.1');
        $host->setMacAddress('00:00:00:00:00:83');
        $host->setVendor('Trafex inc');

        $scanTool = \Mockery::mock('\App\Scan\Tool\Nmap');
        $scanTool->shouldReceive('pingNetwork')
            ->once()
            ->withArgs(array($this->dummyConfig['network'], $this->dummyConfig['interface']))
            ->andReturn(
                array(
                    $host,
                    clone $host,
                )
            );

        $lookup = \Mockery::mock('\App\Lookup\MacAddress');

        $scan = new Scan(
            $entityManager,
            $scanTool,
            $lookup,
            $this->dummyConfig
        );
        $scan->setOutput($this->createOutputMock());

        $this->assertEquals(2, $scan->scanUsingNmap());

    }

    /**
     * @covers ::__construct
     * @covers ::setOutput
     * @covers ::scanUsingNmap
     */
    public function testLookupVendorCalled()
    {
        $device = null;
        $entityManager = $this->createEntityManagerMock($device);

        $host = new Host();
        $host->setIp('127.0.0.1');
        $host->setMacAddress('00:00:00:00:00:83');

        $scanTool = \Mockery::mock('\App\Scan\Tool\Nmap');
        $scanTool->shouldReceive('pingNetwork')
            ->once()
            ->withArgs(array($this->dummyConfig['network'], $this->dummyConfig['interface']))
            ->andReturn(
                array(
                    $host,
                    clone $host,
                )
            );

        $lookup = \Mockery::mock('\App\Lookup\MacAddress');
        $lookup->shouldReceive('getVendorForMacAddress')
            ->with('00:00:00:00:00:83')
            ->andReturn('Trafex inc.');

        $scan = new Scan(
            $entityManager,
            $scanTool,
            $lookup,
            $this->dummyConfig
        );
        $scan->setOutput($this->createOutputMock());

        $this->assertEquals(2, $scan->scanUsingNmap());
    }

    /**
     * @covers ::__construct
     * @covers ::setOutput
     * @covers ::scanUsingNmap
     */
    public function testSkipWithoutMacAddress()
    {
        $device = new Device;
        $entityManager = $this->createEntityManagerMock($device);

        $host1 = new Host();
        $host1->setIp('127.0.0.1');
        $host1->setMacAddress('00:00:00:00:00:83');

        $host2 = new Host();
        $host2->setIp('127.0.0.1');

        $scanTool = \Mockery::mock('\App\Scan\Tool\Nmap');
        $scanTool->shouldReceive('pingNetwork')
            ->once()
            ->withArgs(array($this->dummyConfig['network'], $this->dummyConfig['interface']))
            ->andReturn(
                array(
                    $host1,
                    $host2,
                )
            );

        $lookup = \Mockery::mock('\App\Lookup\MacAddress');

        $scan = new Scan(
            $entityManager,
            $scanTool,
            $lookup,
            $this->dummyConfig
        );
        $scan->setOutput($this->createOutputMock());

        $this->assertEquals(1, $scan->scanUsingNmap());
    }
    private function createOutputMock()
    {
        $output = \Mockery::mock('\Symfony\Component\Console\Output\OutputInterface');
        $output->shouldReceive('writeln');
        return $output;
    }

    private function createEntityManagerMock($repoReturn = null)
    {
        $mock = \Mockery::mock('\Doctrine\ORM\EntityManager');
        $mock->shouldReceive('flush');
        $mock->shouldReceive('clear');

        $mock->shouldReceive('persist')
            ->with(\Mockery::type('\App\Entity\Device'));
        $mock->shouldReceive('persist')
            ->with(\Mockery::type('\App\Entity\DeviceLog'));

        $repo = \Mockery::mock('\Doctrine\ORM\EntityRepository');
        $repo->shouldReceive('findOneBy')
            ->andReturn($repoReturn);

        $mock->shouldReceive('getRepository')
            ->with('\App\Entity\Device')
            ->andReturn($repo);
        return $mock;
    }
}
