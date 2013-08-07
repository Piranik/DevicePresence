<?php
namespace App\Scan\Tool\Fping;

use App\Scan\Host;

class Mapper
{
    public function toEntity($input, Host $entity)
    {
        $entity->setIp(trim(current(explode(':', $input))));
        $entity->setAlive(true);
        return $entity;
    }
}
