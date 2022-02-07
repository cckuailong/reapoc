<?php

namespace Never5\DownloadMonitor\Dependencies\PayPal\Log;

use Never5\DownloadMonitor\Dependencies\Psr\Log\LoggerInterface;

interface PayPalLogFactory
{
    /**
     * Returns logger instance implementing LoggerInterface.
     *
     * @param string $className
     * @return LoggerInterface instance of logger object implementing LoggerInterface
     */
    public function getLogger($className);
}
