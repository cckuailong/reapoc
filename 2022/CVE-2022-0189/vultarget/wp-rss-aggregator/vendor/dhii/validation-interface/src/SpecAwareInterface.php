<?php

namespace Dhii\Validation;

use Traversable;

/**
 * Something that can have a specification retrieved.
 *
 * @since 0.2
 */
interface SpecAwareInterface
{
    /**
     * Retrieves the specification associated with this instance.
     *
     * @since 0.2
     *
     * @return array|Traversable The specification.
     *                           A specification is a set of requirements.
     */
    public function getSpec();
}
