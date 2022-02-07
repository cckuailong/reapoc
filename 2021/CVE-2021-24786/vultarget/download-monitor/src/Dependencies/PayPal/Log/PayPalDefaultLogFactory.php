<?php

namespace Never5\DownloadMonitor\Dependencies\PayPal\Log;

use Never5\DownloadMonitor\Dependencies\Psr\Log\LoggerInterface;

/**
 * Class PayPalDefaultLogFactory
 *
 * This factory is the default implementation of Log factory.
 *
 * @package Never5\DownloadMonitor\Dependencies\PayPal\Log
 */
class PayPalDefaultLogFactory implements PayPalLogFactory
{
    /**
     * Returns logger instance implementing LoggerInterface.
     *
     * @param string $className
     * @return LoggerInterface instance of logger object implementing LoggerInterface
     */
    public function getLogger($className)
    {
        return new PayPalLogger($className);
    }
}
