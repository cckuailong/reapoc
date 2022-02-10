<?php

namespace Dhii\Output\Exception;

use Dhii\Output\ContextAwareInterface;
use Dhii\Output\TemplateInterface;

/**
 * An exception that occurs when cannot render with a context.
 *
 * @since 0.2
 */
interface TemplateRenderExceptionInterface extends
    CouldNotRenderExceptionInterface,
    ContextAwareInterface
{
    /**
     * {@inheritdoc}
     *
     * @since 0.2
     * 
     * @return TemplateInterface The renderer.
     */
    public function getRenderer();
}
