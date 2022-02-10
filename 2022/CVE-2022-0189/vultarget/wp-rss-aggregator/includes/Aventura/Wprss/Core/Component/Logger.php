<?php

namespace Aventura\Wprss\Core\Component;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * A WPRSS-specific implementation, ready to use.
 *
 * @since 4.8.1
 */
class Logger extends NullLogger implements LoggerInterface
{
}
