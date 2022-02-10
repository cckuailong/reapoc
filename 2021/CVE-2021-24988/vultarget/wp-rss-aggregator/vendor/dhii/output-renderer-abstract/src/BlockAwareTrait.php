<?php

namespace Dhii\Output;

use Exception as RootException;
use InvalidArgumentException;
use Dhii\Util\String\StringableInterface as Stringable;

/**
 * Common functionality for objects that are aware of a block.
 *
 * @since 0.1
 */
trait BlockAwareTrait
{
    /**
     * The block instance.
     *
     * @since 0.1
     *
     * @var BlockInterface|null
     */
    protected $block;

    /**
     * Retrieves the block associated with this instance.
     *
     * @since 0.1
     *
     * @return BlockInterface|null The block.
     */
    protected function _getBlock()
    {
        return $this->block;
    }

    /**
     * Sets the block for this instance.
     *
     * @since 0.1
     *
     * @param BlockInterface|null $block The block instance, or null.
     */
    protected function _setBlock($block)
    {
        if ($block !== null && !($block instanceof BlockInterface)) {
            throw $this->_createInvalidArgumentException(
                $this->__('Invalid block'),
                null,
                null,
                $block
            );
        }

        $this->block = $block;
    }

    /**
     * Creates a new Dhii invalid argument exception.
     *
     * @since 0.1
     *
     * @param string|Stringable|null $message  The error message, if any.
     * @param int|null               $code     The error code, if any.
     * @param RootException|null     $previous The inner exception for chaining, if any.
     * @param mixed|null             $argument The invalid argument, if any.
     *
     * @return InvalidArgumentException The new exception.
     */
    abstract protected function _createInvalidArgumentException(
        $message = null,
        $code = null,
        RootException $previous = null,
        $argument = null
    );

    /**
     * Translates a string, and replaces placeholders.
     *
     * @since 0.1
     * @see   sprintf()
     *
     * @param string $string  The format string to translate.
     * @param array  $args    Placeholder values to replace in the string.
     * @param mixed  $context The context for translation.
     *
     * @return string The translated string.
     */
    abstract protected function __($string, $args = [], $context = null);
}
