<?php

namespace RebelCode\Wpra\Core\Logger;

/**
 * An interface for loggers that can be cleared of previously logged messages.
 *
 * @since 4.13
 */
interface ClearableLoggerInterface
{
    /**
     * Clears all previously logged messages.
     *
     * @since 4.13
     *
     * @return void
     */
    public function clearLogs();
}
