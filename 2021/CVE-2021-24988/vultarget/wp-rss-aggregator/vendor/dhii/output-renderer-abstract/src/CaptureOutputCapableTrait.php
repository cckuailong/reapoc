<?php

namespace Dhii\Output;

use Dhii\Exception\InternalExceptionInterface;
use Dhii\Invocation\Exception\InvocationExceptionInterface;
use InvalidArgumentException;
use stdClass;
use Traversable;
use Exception as RootException;

/**
 * Functionality for capturing the output of arbitrary code.
 *
 * @since [*next-version*]
 */
trait CaptureOutputCapableTrait
{
    /**
     * Invokes the given callable, and returns the output as a string.
     *
     * @since [*next-version*]
     *
     * @param callable                        $callable The callable that may produce output.
     * @param array|stdClass|Traversable|null $args     The arguments to invoke the callable with. Defaults to empty array.
     *
     * @throws InvalidArgumentException If the callable or the args list are invalid.
     * @throws RootException            If a problem occurs.
     *
     * @return string The output.
     */
    protected function _captureOutput(callable $callable, $args = null)
    {
        // Default
        if (is_null($args)) {
            $args = [];
        }

        ob_start();
        $this->_invokeCallable($callable, $args);
        $output = ob_get_clean();

        return $output;
    }

    /**
     * Invokes a callable.
     *
     * @since [*next-version*]
     *
     * @param callable                   $callable The callable to invoke.
     * @param array|Traversable|stdClass $args     The arguments to invoke the callable with.
     *
     * @throws InvalidArgumentException     If the callable is not callable.
     * @throws InvalidArgumentException     If the args are not a valid list.
     * @throws InvocationExceptionInterface If the callable cannot be invoked.
     * @throws InternalExceptionInterface   If a problem occurs during invocation.
     *
     * @return mixed The result of the invocation.
     */
    abstract protected function _invokeCallable($callable, $args);
}
