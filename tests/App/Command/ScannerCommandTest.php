<?php
namespace App\Command;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use App\Command\ScannerCommand;

/**
 * ScannerCommandTest
 *
 * @coversDefaultClass \App\Command\ScannerCommand
 * @see \PHPUnit_Framework_TestCase
 * @author Tim de Pater <code@trafex.nl>
 */
class ScannerCommandTest extends \PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        \Mockery::close();
    }

    /**
     * @covers \App\Command\ScannerCommand
     */
    public function testSuccessfulScan()
    {
        $deviceLogRepo = \Mockery::mock('App\Repository\DeviceLogRepository');
        $deviceLogRepo->shouldReceive('cleanupOlderThen')
            ->once()
            ->with(\Mockery::type('\DateTime'))
            ->andReturn(1);

        $failureLimiter = \Mockery::mock('App\Command\FailureLimiter');
        $failureLimiter->shouldReceive('successfull')
            ->once();
        $failureLimiter->shouldReceive('reachedLimit')
            ->once()
            ->andReturn(false);

        $timeBlocks = \Mockery::mock('App\Aggregation\TimeBlocks');
        $timeBlocks->shouldReceive('aggregateToTimeBlocks')
            ->once()
            ->with(10)
            ->andReturn(3);

        $scan = \Mockery::mock('App\Scan');
        $scan->shouldReceive('setOutput')
            ->once();
        $scan->shouldReceive('scanUsingNmap')
            ->once()
            ->andReturn(2);

        $config = array(
            'offlineGap' => 10,
            'interval' => 10,
        );

        $app = new Application();
        $app->add(
            new ScannerCommand(
                $deviceLogRepo,
                $failureLimiter,
                $timeBlocks,
                $scan,
                $config
            )
        );
        $command = $app->find('scanner');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            array('command' => $command->getName(), '--once' => true, '--quiet' => true)
        );
    }

    /**
     * @covers \App\Command\ScannerCommand
     */
    public function testFailureScan()
    {
        $deviceLogRepo = \Mockery::mock('App\Repository\DeviceLogRepository');
        $deviceLogRepo->shouldReceive('cleanupOlderThen')
            ->never();

        $failureLimiter = \Mockery::mock('App\Command\FailureLimiter');
        $failureLimiter->shouldReceive('failure')
            ->once();
        $failureLimiter->shouldReceive('reachedLimit')
            ->once()
            ->andReturn(true);

        $timeBlocks = \Mockery::mock('App\Aggregation\TimeBlocks');
        $timeBlocks->shouldReceive('aggregateToTimeBlocks')
            ->never();

        $scan = \Mockery::mock('App\Scan');
        $scan->shouldReceive('setOutput')
            ->once();
        $scan->shouldReceive('scanUsingNmap')
            ->once()
            ->andThrow('Symfony\Component\Process\Exception\RuntimeException');

        $config = array(
            'offlineGap' => 10,
            'interval' => 10,
        );

        $this->setExpectedException('\RuntimeException');

        $app = new Application();
        $app->add(
            new ScannerCommand(
                $deviceLogRepo,
                $failureLimiter,
                $timeBlocks,
                $scan,
                $config
            )
        );
        $command = $app->find('scanner');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            array('command' => $command->getName(), '--once' => true, '--quiet' => true)
        );
    }
}
