<?php
namespace App\Command;

/**
 * OutputTest
 *
 * @coversDefaultClass \App\Command\Output
 * @see \PHPUnit_Framework_TestCase
 * @author Tim de Pater <code@trafex.nl>
 */
class OutputTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::writeln
     */
    public function testWriteln()
    {
        $output = \Mockery::mock('\App\Command\Output[write]');
        $output->shouldReceive('write')
            ->once()
            ->withArgs(
                array(
                    strftime('%F %T') . ': testmessage',
                    true,
                    'test'
                )
            );

        $output->writeln('testmessage', 'test');
    }

    public function tearDown()
    {
        \Mockery::close();
    }
}
