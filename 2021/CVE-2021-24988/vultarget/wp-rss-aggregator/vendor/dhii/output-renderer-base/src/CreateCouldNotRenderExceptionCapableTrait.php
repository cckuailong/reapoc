<?php

namespace Dhii\Output;

use Dhii\Util\String\StringableInterface as Stringable;
use Exception as RootException;
use Dhii\Output\Exception\CouldNotRenderException;

trait CreateCouldNotRenderExceptionCapableTrait
{
    /**
     * Creates a new render failure exception.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable|null $message  The error message, if any.
     * @param int|null               $code     The error code, if any.
     * @param RootException|null     $previous The inner exception for chaining, if any.
     * @param RendererInterface      $renderer The associated renderer, if any.
     *
     * @return CouldNotRenderException The new exception.
     */
    protected function _createCouldNotRenderException(
        $message = null,
        $code = null,
        RootException $previous = null,
        RendererInterface $renderer = null
    ) {
        return new CouldNotRenderException($message, $code, $previous, $renderer);
    }
}
