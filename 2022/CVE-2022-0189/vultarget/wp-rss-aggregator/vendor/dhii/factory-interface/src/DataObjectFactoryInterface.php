<?php

namespace Dhii\Factory;

/**
 * A factory that allows the creation of data objects.
 *
 * Primarily just defines the way of passing data for the object in the config.
 *
 * @since 0.1
 */
interface DataObjectFactoryInterface extends FactoryInterface
{
    /**
     * The config key that contains the data for the product.
     *
     * @since 0.1
     */
    const K_DATA = 'data';
}
