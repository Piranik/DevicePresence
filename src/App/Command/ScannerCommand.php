<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\RuntimeException;
use App\Scan;
use App\Aggregation\TimeBlocks;
use App\Repository\DeviceLogRepository;

/**
 * Start the scanner
 *
 * @see Command
 * @author Tim de Pater <code@trafex.nl>
 */
class ScannerCommand extends Command
{
    private $config;
    private $deviceLogsRepository;
    private $timeBlocks;
    private $failureLimiter;
    private $scanner;

    /**
     * Constructor
     *
     * @param DeviceLogRepository $deviceLogsRepository
     * @param FailureLimiter $failureLimiter
     * @param TimeBlocks $timeBlocks
     * @param Scan $scanner
     * @param array $config
     */
    public function __construct(
        DeviceLogRepository $deviceLogsRepository,
        FailureLimiter $failureLimiter,
        TimeBlocks $timeBlocks,
        Scan $scanner,
        array $config
    ) {
        $this->deviceLogsRepository = $deviceLogsRepository;
        $this->failureLimiter = $failureLimiter;
        $this->timeBlocks = $timeBlocks;
        $this->scanner = $scanner;
        $this->config = $config;

        parent::__construct();
    }

    /**
     * Configure the CLI arguments
     *
     * @see Command::configure
     */
    protected function configure()
    {
        $this
            ->setName('scanner')
            ->setDescription('Scan for devices');
    }

    /**
     * Execute the command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output = new Output($output);
        $output->writeLn('Starting scanner..');
        $this->scanner->setOutput($output);

        while (true) {
            try {
                $this->scan($output);
                $this->aggregateToTimeBlocks($output);
                $this->cleanupOldTimeBlocks($output);
                $this->failureLimiter->successfull();

            } catch (RuntimeException $e) {
                // Some timeouts on the process are acceptable
                $output->writeLn(
                    sprintf(
                        '<error>%s: %s</error>',
                        get_class($e),
                        $e->getMessage()
                    )
                );
                $output->writeLn(
                    sprintf(
                        'Increased failure count to %s',
                        $this->failureLimiter->failure()
                    )
                );
            }

            if ($this->failureLimiter->reachedLimit()) {
                throw new \RuntimeException('The process reached the maximum failure limit', 0, $e);
            }
            sleep($this->config['interval']);
        }
    }

    /**
     * Execute the scanner
     *
     * @param OutputInterface $output
     */
    private function scan(OutputInterface $output)
    {
        $time = microtime(true);
        $devices = $this->scanner->scanUsingNmap();
        $output->writeLn(
            sprintf(
                '<info>Updated %u devices</info> (took: %01.2f secs, used memory: %01.2fMB)',
                $devices,
                microtime(true) - $time,
                memory_get_usage(true)/1048576
            )
        );
    }

    /**
     * Aggregate the devicelogs to timeblocks
     *
     * @param OutputInterface $output
     * @return integer
     */
    private function aggregateToTimeBlocks(OutputInterface $output)
    {
        // @todo: Looks likes this is causing a little memory leak
        $count = $this->timeBlocks->aggregateToTimeBlocks($this->config['offlineGap']);
        $output->writeLn(
            sprintf('Aggregated rows to %u timeblocks', $count)
        );
        return $count;
    }

    /**
     * Remove the old devicelogs, those are already saved in ElasticSearch as
     * timeblock.
     *
     * @param OutputInterface $output
     * @return integer
     */
    private function cleanupOldTimeBlocks(OutputInterface $output)
    {
        $date = new \DateTime();
        $date->sub(new \DateInterval('P1W'));
        $removed = $this->deviceLogsRepository->cleanupOlderThen($date);
        if ($removed > 1) {
            $output->writeLn(
                sprintf('Cleaned up %u devicelogs older then %s', $removed, $date->format('Y-m-d'))
            );
        }
        return $removed;
    }
}
