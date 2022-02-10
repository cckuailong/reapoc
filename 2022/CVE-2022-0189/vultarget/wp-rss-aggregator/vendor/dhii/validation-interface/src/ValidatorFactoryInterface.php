<?php

namespace Dhii\Validation;

use Dhii\Factory\FactoryInterface;

/**
 * Something that can create validator instances.
 *
 * @since 0.2
 */
interface ValidatorFactoryInterface extends FactoryInterface
{
    /**
     * {@inheritdoc}
     *
     * @since 0.2
     *
     * @return ValidatorInterface The created validator instance.
     */
    public function make($config = null);
}
