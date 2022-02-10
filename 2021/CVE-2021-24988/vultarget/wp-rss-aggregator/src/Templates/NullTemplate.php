<?php

namespace RebelCode\Wpra\Core\Templates;

use Dhii\Output\TemplateInterface;

/**
 * A template implementation that does nothing.
 *
 * @since 4.13
 */
class NullTemplate implements TemplateInterface
{
    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    public function render($context = null)
    {
    }
}
