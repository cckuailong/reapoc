<?php

namespace RebelCode\Wpra\Core\Handlers;

/**
 * A simple handler that echoes a string.
 *
 * @since 4.17
 */
class EchoHandler
{
    /**
     * @since 4.17
     *
     * @var string
     */
    protected $string;

    /**
     * Constructor.
     *
     * @since 4.17
     *
     * @param string $string The string to output.
     */
    public function __construct($string)
    {
        $this->string = $string;
    }

    /**
     * @inheritdoc
     *
     * @since 4.17
     */
    public function __invoke()
    {
        echo $this->string;
    }
}
