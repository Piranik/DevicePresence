<?php
namespace App\Command;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

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

        $scanner = new \App\Scan($this->entityManager, $output, $this->config);
        while (true) {
            $output->writeLn(
                sprintf(
                    '<info>Updated %u devices</info> (used memory: %01.2fMB)',
                    $scanner->scanUsingNmap(),
                    memory_get_usage(true)/1048576
                )
            );
            sleep($this->config['interval']);
        }
    }
}
