<?php

namespace Dhii\Transformer\Exception;

use Dhii\Exception\ThrowableInterface;
use Dhii\Transformer\TransformerAwareInterface;
use Dhii\Transformer\TransformerInterface;

/**
 * An exception thrown in relation to a transformer.
 *
 * @since [*next-version*]
 */
interface TransformerExceptionInterface extends
    ThrowableInterface,
    TransformerAwareInterface
{
}
