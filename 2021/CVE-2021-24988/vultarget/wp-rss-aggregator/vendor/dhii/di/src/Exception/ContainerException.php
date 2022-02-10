<?php

namespace Dhii\Di\Exception;

use Interop\Container\Exception\ContainerException as ContainerExceptionInterface;
use Dhii\Di\ExceptionInterface as DiExceptionInterface;

/**
 * An exception related to DI containers.
 *
 * @since 0.1
 */
class ContainerException extends \Exception implements
    DiExceptionInterface,
    ContainerExceptionInterface
{
}
