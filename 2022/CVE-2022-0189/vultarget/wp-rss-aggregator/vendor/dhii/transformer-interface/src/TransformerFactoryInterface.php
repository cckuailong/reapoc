<?php

namespace Dhii\Transformer;

use Dhii\Factory\FactoryInterface;

/**
 * A factory of transformers.
 *
 * @since [*next-version*]
 */
interface TransformerFactoryInterface extends FactoryInterface
{
    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     *
     * @return TransformerInterface The new transformer.
     */
    public function make($config = null);
}
