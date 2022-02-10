<?php

namespace Dhii\Output;

use Dhii\Util\String\StringableInterface as Stringable;
use Psr\Container\ContainerInterface;
use Dhii\Output\Exception\RendererExceptionInterface;
use Dhii\Output\Exception\TemplateRenderExceptionInterface;

/**
 * Functionality for rendering a template.
 *
 * @since 0.1
 */
trait RenderTemplateCapableTrait
{
    /**
     * Renders a template with context.
     *
     * @param TemplateInterface             $template The template to render.
     * @param ContainerInterface|array|null $context  The context to use for rendering.
     *
     * @since 0.1
     *
     * @throws TemplateRenderExceptionInterface The template may throw this if a problem occurs.
     * @throws RendererExceptionInterface       The template may throw this if a problem specific to rendering occurs.
     *
     * @return string|Stringable The rendered output.
     */
    protected function _renderTemplate(TemplateInterface $template, $context = null)
    {
        return $template->render($context);
    }
}
