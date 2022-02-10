<?php

namespace RebelCode\Wpra\Core\Logger;

use Psr\Log\NullLogger;

/**
 * A special null logger that is used when an error occurs and the original logger cannot be used.
 *
 * @since 4.15
 */
class ProblemLogger extends NullLogger implements LogReaderInterface, ClearableLoggerInterface
{
    /**
     * The error.
     *
     * @since 4.15
     *
     * @var string
     */
    public $error;

    /**
     * Constructor.
     *
     * @since 4.15
     *
     * @param string $error The error.
     */
    public function __construct($error)
    {
        $this->error = $error;
    }

    /**
     * @inheritdoc
     *
     * @since 4.15
     */
    public function clearLogs()
    {
    }

    /**
     * @inheritdoc
     *
     * @since 4.15
     */
    public function getLogs($num = null, $page = 1)
    {
        return [];
    }
}
