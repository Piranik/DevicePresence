<?php
namespace App\Lookup;

class MacAddressTest extends \PHPUnit_Framework_TestCase
{
    public function testCanWeFetchAValidVendor()
    {
        $apiKey = 'APIKEY';
        $macAddr = '5C:0A:55:55:55:55';
        $mock = $this->getMockObject($macAddr, $apiKey);

        $this->assertEquals(
            'SAMSUNG ELECTRO-MECHANICS CO., LTD.',
            $mock->getVendorForMacAddress($macAddr)
        );
    }

    public function testNoResultsWithNoApiKey()
    {
        $apiKey = null;
        $macAddr = '5C:0A:55:55:55:55';
        $mock = $this->getMockObject($macAddr, $apiKey);

        $this->assertNull($mock->getVendorForMacAddress($macAddr));
    }

    public function testNoResultsWhenNoResponse()
    {
        $apiKey = 'APIKEY';
        $macAddr = '5C:0A:55:55:55:55';
        $mock = $this->getMock('\App\Lookup\MacAddress', array('fetchFromApi'), array($apiKey));
        $mock->expects($this->any())
            ->method('fetchFromApi')
            ->with($this->equalTo($macAddr))
            ->will($this->returnValue(false));

        $this->assertNull($mock->getVendorForMacAddress($macAddr));
    }

    private function getMockObject($macAddr, $apiKey)
    {
        $mock = $this->getMock('\App\Lookup\MacAddress', array('fetchFromApi'), array($apiKey));
        $mock->expects($this->any())
            ->method('fetchFromApi')
            ->with($this->equalTo($macAddr))
            ->will($this->returnValue(
              '[{"starthex":"5C0A5B000000","endhex":"5C0A5BFFFFFF","startdec":"101199546155008",
              "enddec":"101199562932223","company":"SAMSUNG ELECTRO-MECHANICS CO., LTD.",
              "department":"314, Maetan3-Dong, Yeongtong-Gu","address1":"",
              "address2":"Suwon Gyunggi-Do 443-743","country":"KOREA, REPUBLIC OF","db":"oui24"}]'
            ));
        return $mock;
    }
}
