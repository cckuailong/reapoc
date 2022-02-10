<?php

namespace Dhii\Transformer\Exception;

/**
 * An exception thrown in relation to a transformer when it fails to transform the given source data.
 *
 * @since [*next-version*]
 */
interface CouldNotTransformExceptionInterface extends TransformerExceptionInterface
{
    /**
     * Retrieves the source data that the transformer failed to transform.
     *
     * @since [*next-version*]
     *
     * @return mixed|null The data that is being transformed.
     */
    public function getSourceData();
}
