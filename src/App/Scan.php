<?php
namespace App;

use App\Scan\Tool\Fping;

class Scan
{
    const NETWORK = '172.17.0.210/29';

    public function scan()
    {
        $tool = new Fping();
        $result = $tool->pingNetwork(self::NETWORK);
        return $result;
    }
}
