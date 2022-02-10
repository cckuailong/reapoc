<?php

namespace Aventura\Wprss\Core\Model;

/**
 * An interface of something that can spin post data.
 *
 * @since 4.8.1
 */
interface SpinnerInterface
{
    /**
     *
     * @since 4.8.1
     * @param string $content
     */
    public function spin($content);
}