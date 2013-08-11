<?php
namespace App\Command;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\RuntimeException;
use App\Scan;

/**
 * Start the scanner
 *
 * @see Command
 * @author Tim de Pater <code@trafex.nl>
 */
class ScannerCommand extends Command
{
    private $config;
    private $entityManager;

    public function __construct(EntityManager $em, array $config)
    {
        $this->entityManager = $em;
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

        $failureLimiter = new FailureLimiter();

        $scanner = new Scan($this->entityManager, $output, $this->config);
        while (true) {
            try {
                $this->scan($scanner, $output);
                $failureLimiter->successfull();

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
                        $failureLimiter->failure()
                    )
                );
            }

            if ($failureLimiter->reachedLimit()) {
                throw new \RuntimeException('The process reached the maximum failure limit' , 0, $e);
            }
            sleep($this->config['interval']);
        }
    }

    /**
     * Execute the scanner
     *
     * @param Scan $scanner
     * @param OutputInterface $output
     */
    private function scan(Scan $scanner, OutputInterface $output)
    {
        $time = microtime(true);
        $devices = $scanner->scanUsingNmap();
        $output->writeLn(
            sprintf(
                '<info>Updated %u devices</info> (took: %01.2f secs, used memory: %01.2fMB)',
                $devices,
                microtime(true) - $time,
                memory_get_usage(true)/1048576
            )
        );
    }
}
