<?php

namespace Aventura\Wprss\Core\Model;

/**
 * An interface of something that can be a command.
 *
 * @since [*next-version]
 */
interface CommandInterface
{
    /**
     * Makes it possible to invoke an instance of the implementing class
     * as if it was a function.
     *
     * @since 4.8.1
     * @return mixed The result of the call
     */
    public function __invoke();
}