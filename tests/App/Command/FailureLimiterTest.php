<?php
namespace App\Command;
use App\Command\FailureLimiter;

class FailureLimiterTest extends \PHPUnit_Framework_TestCase
{
    public function testReachingLimit()
    {
        $limiter = new FailureLimiter();
        $limiter->setLimit(2);
        $this->assertEquals(2, $limiter->getLimit());

        $this->assertFalse($limiter->reachedLimit());

        $this->assertEquals(0, $limiter->successfull());
        $this->assertFalse($limiter->reachedLimit());

        $this->assertEquals(1, $limiter->failure());
        $this->assertFalse($limiter->reachedLimit());

        $this->assertEquals(2, $limiter->failure());
        $this->assertTrue($limiter->reachedLimit());
    }

    public function testRestoringAfterLimit()
    {
        $limiter = new FailureLimiter();
        $limiter->setLimit(2);

        $this->assertEquals(1, $limiter->failure());
        $this->assertFalse($limiter->reachedLimit());

        $this->assertEquals(2, $limiter->failure());
        $this->assertTrue($limiter->reachedLimit());

        $this->assertEquals(3, $limiter->failure());
        $this->assertTrue($limiter->reachedLimit());

        $this->assertEquals(2, $limiter->successfull());
        $this->assertTrue($limiter->reachedLimit());

        $this->assertEquals(1, $limiter->successfull());
        $this->assertFalse($limiter->reachedLimit());

        $this->assertEquals(0, $limiter->successfull());
        $this->assertFalse($limiter->reachedLimit());
        $this->assertEquals(0, $limiter->successfull());
    }
}
