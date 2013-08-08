<?php

namespace App;

class ProcessManager
{
    /**
     * Forked childs
     *
     * @var array
     */
    private $childs = array();

    /**
     * Pid of pcntl_fork()
     *
     * @var int
     */
    private $pid;

    /**
     * Max number of childs
     *
     * @var int
     */
    private $maxChilds = 1;

    /**
     * Logger to be used
     *
     * @var \Glitch_Log
     */
    private $logger;

    /**
     * Constructs the class with the correct logger
     *
     * @param mixed $logger
     * @return void
     */
    public function __construct($logger = null)
    {
        if (null === $logger) {
            $logger = \Glitch_Registry::getLog();
        }
        $this->logger = $logger;
    }

    /**
     * Get maxChildslds.
     *
     * @return maxChilds.
     */
    public function getMaxChilds()
    {
        return $this->maxChilds;
    }

    /**
     * Set maxChilds.
     *
     * @param maxChilds the value to set.
     */
    public function setMaxChilds($maxChilds)
    {
        $this->maxChilds = $maxChilds;
    }

    /**
     * Run the given lamda function in a fork
     *
     * @param callable $execute
     * @return void
     */
    public function run($execute)
    {
        if ($this->fork()) {
            try {
                $execute();
            } catch (\Exception $e) {
                $this->logger->err(
                    sprintf('Exception while executing: %s: %s. Trace: %s', get_class($e), $e->getMessage(), $e->getTraceAsString())
                );
            }
            exit;
        }
        if (count($this->childs) >= $this->maxChilds) {
            $this->logger->debug(
                sprintf(
                    'Running %u childs, wait for one to exit before continue',
                    $this->maxChilds
                )
            );
            $this->waitForChildToExit();
        }
        $this->cleanup();
    }

    /**
     * Returns if this process is a child
     *
     * @return bool
     */
    public function isChild()
    {
        return (0 === $this->pid);
    }

    /**
     * Returns if this process is the parent
     *
     * @return bool
     */
    public function isParent()
    {
        return (0 < $this->pid);
    }

    /**
     * Add a new forked child
     *
     * @param int $pid
     * @return void
     */
    private function addChild($pid)
    {
        $this->childs[] = $pid;
    }

    /**
     * Remove a forked child
     *
     * @param int $pid
     * @return void
     */
    private function removeChild($pid)
    {
        unset($this->childs[array_search($pid, $this->childs)]);
    }

    /**
     * Fork this process
     *
     * @return bool
     */
    private function fork()
    {
        $this->pid = pcntl_fork();
        if ($this->pid < 0) {
            throw new \RuntimeException('Couldn\'t fork');
        }
        if ($this->isParent()) {
            $this->addChild($this->pid);
            $this->logger->debug(
                sprintf('Started child nr %u, pid %s', count($this->childs), $this->pid)
            );
        }
        if ($this->isChild()) {
            return true;
        }
    }

    /**
     * Wait for a child to exit
     *
     * @return void
     */
    private function waitForChildToExit()
    {
        $status = null;
        $child = pcntl_waitpid(-1, $status);
        $this->removeChild($child);
        $this->logger->debug(
            sprintf('Child %u done, status %s', $child, var_export($status, true))
        );
    }

    /**
     * Cleanup the childs that have exited
     *
     * @return void
     */
    private function cleanup()
    {
        $status = null;
        $gone = pcntl_waitpid(-1, $status, WNOHANG OR WUNTRACED);
        while($gone > 0) {
            $this->removeChild($gone);
            $this->logger->debug(
                sprintf('Child %u gone, status %s', $gone, var_export($status, true))
            );
            // Look for another one
            $gone = pcntl_waitpid(-1, $status, WNOHANG OR WUNTRACED);
        }
    }
}
