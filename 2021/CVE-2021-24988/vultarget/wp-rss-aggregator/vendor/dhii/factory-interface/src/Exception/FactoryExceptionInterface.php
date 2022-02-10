<?php

namespace Dhii\Factory\Exception;

use Dhii\Exception\ThrowableInterface;
use Dhii\Factory\FactoryAwareInterface;

/**
 * An exception thrown by a factory when an error has occurred.
 *
 * @since 0.1
 */
interface FactoryExceptionInterface extends
    ThrowableInterface,
    FactoryAwareInterface
{
}
