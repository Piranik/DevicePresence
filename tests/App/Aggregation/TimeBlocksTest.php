<?php
namespace App\Aggregation;

use App\Aggregation\DeviceLogs as DeviceLogAggregator;

/**
 * TimeBlocksTest
 *
 * @coversDefaultClass \App\Aggregation\TimeBlocks
 * @see \PHPUnit_Framework_TestCase
 * @author Tim de Pater <code@trafex.nl>
 */
class TimeBlocksTest extends \PHPUnit_Framework_TestCase
{
    const OFFLINE_GAP = 10;

    public function tearDown()
    {
        \Mockery::close();
    }

    /**
     * @covers ::__construct
     * @covers ::aggregateToTimeBlocks
     */
    public function testDoNothingWithNoLogs()
    {
        $repository = \Mockery::mock('\App\Repository\DeviceLogRepository');
        $repository->shouldReceive('findByDay')
            ->andReturn(array());
        $aggregator = \Mockery::mock('\App\Aggregation\DeviceLogs');
        $aggregator->shouldReceive('aggregate')
            ->with(array(), self::OFFLINE_GAP)
            ->andReturn(array());
        $elasticsearch = \Mockery::mock('\Elastica\Client');

        $timeblock = new TimeBlocks($repository, $aggregator, $elasticsearch);

        $this->assertEquals(0, $timeblock->aggregateToTimeBlocks(self::OFFLINE_GAP));
    }

    /**
     * @covers ::__construct
     * @covers ::aggregateToTimeBlocks
     */
    public function testAddNewDocuments()
    {
        $date = new \DateTime('now');
        $dummyData = array(
            '00:00:00:00:00:01' => array(
                0 => array(
                    'ip' => '127.0.0.1',
                    'device' => '00:00:00:00:00:01',
                    'start' => $date,
                    'end' => $date,
                ),
            ),
            '00:00:00:00:00:02' => array(
                0 => array(
                    'ip' => '127.0.0.2',
                    'device' => '00:00:00:00:00:02',
                    'start' => $date,
                    'end' => $date,
                )
            )
        );

        $repository = \Mockery::mock('\App\Repository\DeviceLogRepository');
        $repository->shouldReceive('findByDay')
            ->andReturn(array());
        $aggregator = \Mockery::mock('\App\Aggregation\DeviceLogs');
        $aggregator->shouldReceive('aggregate')
            ->with(array(), self::OFFLINE_GAP)
            ->andReturn($dummyData);

        $elasticaType = \Mockery::mock('\Elastica\Type');
        $elasticaType->shouldReceive('addDocuments')
            ->with(\Mockery::type('array'));
        $elasticaType->shouldReceive('getIndex->refresh')
            ->once();

        $elasticsearch = \Mockery::mock('\Elastica\Client');
        $elasticsearch->shouldReceive('getIndex->getType')
            ->andReturn($elasticaType);

        $timeblock = new TimeBlocks($repository, $aggregator, $elasticsearch);

        $this->assertEquals(2, $timeblock->aggregateToTimeBlocks(self::OFFLINE_GAP));
    }
}
