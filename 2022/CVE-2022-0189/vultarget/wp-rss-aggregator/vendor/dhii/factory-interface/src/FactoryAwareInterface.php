<?php

namespace Dhii\Factory;

/**
 * Something that can have a factory retrieved from it.
 *
 * @since 0.1
 */
interface FactoryAwareInterface
{
    /**
     * Retrieves the factory associate with this instance.
     *
     * @since 0.1
     *
     * @return FactoryInterface|null
     */
    public function getFactory();
}
