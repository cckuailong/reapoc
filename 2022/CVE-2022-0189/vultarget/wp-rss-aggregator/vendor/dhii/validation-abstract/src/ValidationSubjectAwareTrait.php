<?php

namespace Dhii\Validation;

/**
 * Functionality for retrieving the subject.
 *
 * @since [*next-version*]
 */
trait ValidationSubjectAwareTrait
{
    /**
     * The subject.
     *
     * @since [*next-version*]
     *
     * @var mixed
     */
    protected $validationSubject;

    /**
     * Retrieves the subject associated with this instance.
     *
     * @since [*next-version*]
     *
     * @return mixed The subject.
     */
    protected function _getValidationSubject()
    {
        return $this->validationSubject;
    }

    /**
     * Assigns a subject to this instance.
     *
     * @since [*next-version*]
     *
     * @param mixed $subject The subject.
     */
    protected function _setValidationSubject($subject)
    {
        $this->validationSubject = $subject;
    }
}
