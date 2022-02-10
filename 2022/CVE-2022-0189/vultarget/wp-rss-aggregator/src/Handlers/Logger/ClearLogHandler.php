<?php

namespace RebelCode\Wpra\Core\Handlers\Logger;

use RebelCode\Wpra\Core\Logger\ClearableLoggerInterface;

/**
 * Handles log clearing requests.
 *
 * @since 4.14
 */
class ClearLogHandler
{
    /**
     * @since 4.14
     *
     * @var ClearableLoggerInterface
     */
    protected $logger;

    /**
     * @since 4.14
     *
     * @var string
     */
    protected $nonceName;

    /**
     * Constructor.
     *
     * @since 4.14
     *
     * @param ClearableLoggerInterface $logger    The logger.
     * @param string                   $nonceName The name of the nonce to verify requests.
     */
    public function __construct(ClearableLoggerInterface $logger, $nonceName)
    {
        $this->logger = $logger;
        $this->nonceName = $nonceName;
    }

    /**
     * @inheritdoc
     *
     * @since 4.14
     */
    public function __invoke()
    {
        $clearLog = filter_input(INPUT_POST, 'wpra-clear-log', FILTER_DEFAULT);

        if (empty($clearLog) || !check_admin_referer($this->nonceName)) {
            return;
        }

        $this->logger->clearLogs();
    }
}
