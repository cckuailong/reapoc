<?php
/**
 * Created by PhpStorm.
 * User: xedin
 * Date: 1/10/2018
 * Time: 6:53 PM.
 */

namespace Dhii\Output;

use Dhii\Util\String\StringableInterface as Stringable;
use Exception as RootException;
use Dhii\Output\Exception\RendererException;

trait CreateRendererExceptionCapableTrait
{
    /**
     * Creates a new render-related exception.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable|null $message  The error message, if any.
     * @param int|null               $code     The error code, if any.
     * @param RootException|null     $previous The inner exception for chaining, if any.
     * @param RendererInterface      $renderer The associated renderer, if any.
     *
     * @return RendererException The new exception.
     */
    protected function _createRendererException(
        $message = null,
        $code = null,
        RootException $previous = null,
        RendererInterface $renderer = null
    ) {
        return new RendererException($message, $code, $previous, $renderer);
    }
}
