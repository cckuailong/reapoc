<?php

namespace Dhii\Output;

use Dhii\Util\String\StringableInterface as Stringable;
use Exception as RootException;

/**
 * Common abstract functionality for blocks.
 *
 * @since [*next-version*]
 */
trait StringableRenderCatcherTrait
{
    /**
     * Attempts to render, catering for errors.
     *
     * @link http://php.net/manual/en/language.oop5.magic.php#language.oop5.magic.tostring
     *
     * @return string The rendered output.
     */
    public function __toString()
    {
        try {
            return (string) $this->_render();
        } catch (RootException $exception) {
            return (string) $this->_renderException($exception);
        }
    }

    /**
     * Attempts to produce output.
     *
     * @since [*next-version*]
     *
     * @throws RootException If an error occurs.
     *
     * @return string|Stringable The output.
     */
    abstract protected function _render();

    /**
     * Produces output that represents an exception.
     *
     * @since [*next-version*]
     *
     * @param RootException $exception The exception to render.
     *
     * @return string|Stringable The output.
     */
    abstract protected function _renderException(RootException $exception);
}
