<?php

namespace Dhii\Exception;

/**
 * An exception that occurs in relation to a wrong, invalid, or erroneous value.
 *
 * @since 0.2
 */
interface BadSubjectExceptionInterface extends ThrowableInterface
{
    /**
     * Retrieves the problematic subject.
     *
     * @since 0.2
     *
     * @return mixed|null The subject, if any.
     */
    public function getSubject();
}
