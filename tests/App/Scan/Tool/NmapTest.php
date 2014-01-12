<?php
namespace App\Scan\Tool;

use App\Scan\Tool\Nmap;

/**
 * NmapTest
 *
 * @coversDefaultClass \App\Scan\Tool\Nmap
 * @see \PHPUnit_Framework_TestCase
 * @author Tim de Pater <code@trafex.nl>
 */
class NmapTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \App\Scan\Tool\Nmap
     * @covers \App\Scan\Tool\Nmap\Mapper
     */
    public function testPingANetwork()
    {
        $network = '192.168.150.0/24';
        $program = $this->getMock('\App\Scan\Tool\Nmap\Program');
        $program->expects($this->any())
            ->method('nmap')
            ->with($this->equalTo($network))
            ->will(
                $this->returnValue(
                    file_get_contents(__DIR__ . '/../../../mock/nmapoutput.txt')
                )
            );
        $nmap = new Nmap();
        $nmap->setProgram($program);
        $hosts = $nmap->pingNetwork($network);
        $this->assertCount(3, $hosts);

        $host = current($hosts);
        $this->assertInstanceOf('App\Scan\Host', $host);
        $this->assertEquals('192.168.150.1', $host->getIp());
        $this->assertEquals('00:00:00:00:00:78', $host->getMacAddress());
        $this->assertNotNull($host->getVendor());

        $host = next($hosts);
        $this->assertInstanceOf('App\Scan\Host', $host);
        $this->assertEquals('192.168.150.21', $host->getIp());
        $this->assertNull($host->getMacAddress());
        $this->assertNull($host->getVendor());

        $host = next($hosts);
        $this->assertInstanceOf('App\Scan\Host', $host);
        $this->assertEquals('192.168.150.42', $host->getIp());
        $this->assertEquals('00:00:00:00:00:83', $host->getMacAddress());
        $this->assertNotNull($host->getVendor());
    }

    /**
     * @covers \App\Scan\Tool\Nmap
     * @covers \App\Scan\Tool\Nmap\Mapper
     */
    public function testPingInvalidNetwork()
    {
        $network = '192.168.150.0/24';
        $program = $this->getMock('\App\Scan\Tool\Nmap\Program');
        $program->expects($this->any())
            ->method('nmap')
            ->with($this->equalTo($network))
            ->will(
                $this->returnValue(
                    file_get_contents(__DIR__ . '/../../../mock/failednmap.txt')
                )
            );
        $nmap = new Nmap();
        $nmap->setProgram($program);
        $hosts = $nmap->pingNetwork($network);
        $this->assertCount(0, $hosts);
    }
}
