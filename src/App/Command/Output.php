<?php
namespace App\Command;

use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * Wrapper around ConsoleOutput to have a timestamp with every message
 *
 * @see ConsoleOutput
 * @author Tim de Pater <code@trafex.nl>
 */
class Output extends ConsoleOutput
{
    /**
     * {@inheritdoc}
     */
    public function writeln($messages, $type = parent::OUTPUT_NORMAL)
    {
        $this->write(sprintf(
            '%s: %s',
            strftime('%F %T'),
            $messages
        ), true, $type);
    }
}
