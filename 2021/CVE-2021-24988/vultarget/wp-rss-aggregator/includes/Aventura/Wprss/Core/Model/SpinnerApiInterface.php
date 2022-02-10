<?php

namespace Aventura\Wprss\Core\Model;

/**
 * Something that can spin content, usually using a remote service.
 *
 * @since 4.8.1
 */
interface SpinnerApiInterface
{
    /**
     * Spins the content using the remote API.
     *
     * @since 4.8.1
     * @param mixed $content The content to spin.
     * @param array $options Options for spinning.
     */
    public function spin($content, $options = array());
}