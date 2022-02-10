<?php

namespace Dhii\Factory;

/**
 * Something that creates factories.
 *
 * @since 0.1
 */
interface FactoryFactoryInterface extends FactoryInterface
{
    /**
     * {@inheritdoc}
     *
     * @since 0.1
     *
     * @return FactoryInterface The new factory.
     */
    public function make($config = null);
}
