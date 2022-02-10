<?php

namespace Dhii\Output;

use Exception as RootException;
use Dhii\Util\String\StringableInterface as Stringable;

trait RenderExceptionCapableToStringTrait
{
    /**
     * Produces output that represents an exception.
     *
     * @since [*next-version*]
     *
     * @param RootException $exception The exception to render.
     *
     * @return string|Stringable The output.
     */
    protected function _renderException(RootException $exception)
    {
        return $exception->__toString();
    }
}
