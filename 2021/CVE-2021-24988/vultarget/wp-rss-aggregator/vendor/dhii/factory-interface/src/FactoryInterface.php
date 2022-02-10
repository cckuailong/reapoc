<?php

namespace Dhii\Factory;

use ArrayAccess;
use Dhii\Factory\Exception\FactoryExceptionInterface;
use Dhii\Factory\Exception\CouldNotMakeExceptionInterface;
use Psr\Container\ContainerInterface;
use stdClass;

/**
 * Something that can create things.
 *
 * @since 0.1
 */
interface FactoryInterface
{
    /**
     * Creates a new instance.
     *
     * @since 0.1
     *
     * @param array|ArrayAccess|ContainerInterface|stdClass|null $config A map of configuration, if any. Any container
     *                                                                   that passes normalization by {@see Dhii\Data\Container\ContainerGetCapableTrait#_containerGet()}.
     *
     * @throws CouldNotMakeExceptionInterface If the factory failed to make the instance.
     * @throws FactoryExceptionInterface      If an error related to the factory occurs.
     *
     * @return mixed The created things.
     */
    public function make($config = null);
}
