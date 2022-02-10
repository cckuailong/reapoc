<?php

namespace Dhii\Transformer;

/**
 * Something that exposes a transformer.
 *
 * @since [*next-version*]
 */
interface TransformerAwareInterface
{
    /**
     * Retrieves the transformer associated with this instance.
     *
     * @since [*next-version*]
     *
     * @return TransformerInterface|null
     */
    public function getTransformer();
}
