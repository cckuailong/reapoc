<?php

namespace Dhii\Exception;

/**
 * Something that can have an argument code retrieved from it.
 *
 * Can be useful for argument-related exceptions that need to provide more
 * specific information about the argument.
 *
 * @since 0.1
 */
interface ArgumentCodeAwareInterface
{
    /**
     * Retrieves the argument code associated with this instance.
     *
     * @since 0.1
     *
     * @return string|int The argument code. Something that identifies an argument
     *                    in its scope.
     */
    public function getArgumentCode();
}
