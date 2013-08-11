<?php

namespace App\Command;

/**
 * Failure limiter
 *
 * Can be used to allow some failures, but don't allow only failures
 *
 * @author Tim de Pater <code@trafex.nl>
 */
class FailureLimiter
{
    /**
     * Failure limit
     *
     * @var int
     */
    private $limit = 3;

    /**
     * Failure points
     *
     * @var int
     */
    private $points = 0;

    /**
     * Increase successfull
     *
     * @return int
     */
    public function successfull()
    {
        if ($this->points > 0) {;
            $this->points--;
        }
        return $this->points;
    }

    /**
     * Increase failure
     *
     * @return int
     */
    public function failure()
    {
        return ++$this->points;
    }

    /**
     * Are there more failures then allowed?
     *
     * @return bool
     */
    public function reachedLimit()
    {
        return $this->limit <= $this->points;
    }

    /**
     * Get the failure limit
     *
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Set the failure limit
     *
     * @param int $limit
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
    }
}
