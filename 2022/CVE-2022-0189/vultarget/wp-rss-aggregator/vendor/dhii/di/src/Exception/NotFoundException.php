<?php

namespace Dhii\Di\Exception;

use Interop\Container\Exception\NotFoundException as NotFoundExceptionInterface;
use Dhii\Di\ExceptionInterface as DiExceptionInterface;

/**
 * An exception that is thrown when a service definition is not found by a DI container.
 *
 * @since 0.1
 */
class NotFoundException extends \Exception implements
    DiExceptionInterface,
    NotFoundExceptionInterface
{
}
