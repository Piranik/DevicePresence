<?php
namespace App\Scan\Tool\Nmap;

use App\Scan\Host;

class Mapper
{
    public function toEntity(\DOMXpath $xpath, $host, Host $entity)
    {
        $entity->setIp($xpath->query('./address[@addrtype="ipv4"]/@addr', $host)->item(0)->nodeValue);
        $mac = $xpath->query('./address[@addrtype="mac"]/@addr', $host);
        if ($mac->length > 0) {
            $entity->setMacAddress($mac->item(0)->nodeValue);
        }
        $vendor = $xpath->query('./address[@addrtype="mac"]/@vendor', $host);
        if ($vendor->length > 0) {
            $entity->setVendor($vendor->item(0)->nodeValue);
        }
        return $entity;
    }
}

