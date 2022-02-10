<?php

namespace Dhii\Exception;

/**
 * Functionality for subject awareness.
 *
 * @since [*next-version*]
 */
trait SubjectAwareTrait
{
    /**
     * The subject associated with this instance.
     *
     * @since [*next-version*]
     *
     * @var mixed
     */
    protected $subject;

    /**
     * Retrieves the subject.
     *
     * @since [*next-version*]
     *
     * @return mixed The subject
     */
    protected function _getSubject()
    {
        return $this->subject;
    }

    /**
     * Assigns the subject.
     *
     * @since [*next-version*]
     *
     * @return $this
     */
    protected function _setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }
}
