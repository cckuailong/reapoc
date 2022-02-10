<?php

namespace Dhii\Validation;

/**
 * Something that can have a subject retrieved.
 *
 * @since 0.2
 */
interface SubjectAwareInterface
{
    /**
     * Retrieves the subject.
     *
     * @since 0.2
     *
     * @return mixed The subject.
     */
    public function getSubject();
}
