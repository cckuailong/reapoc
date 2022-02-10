<?php

namespace Dhii\Factory\Exception;

use Psr\Container\ContainerInterface;

/**
 * An exception thrown when a factory failed to create an instance.
 *
 * @since 0.1
 */
interface CouldNotMakeExceptionInterface extends
    FactoryExceptionInterface
{
    /**
     * Retrieves the configuration of the subject that failed.
     *
     * @since 0.1
     *
     * @return array|ContainerInterface
     */
    public function getSubjectConfig();
}
