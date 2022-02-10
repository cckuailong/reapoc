<?php

namespace Dhii\Output;

use Dhii\Output\Exception\TemplateRenderExceptionInterface;
use Exception as RootException;
use Dhii\Output\Exception\CouldNotRenderExceptionInterface;
use Dhii\Output\Exception\RendererExceptionInterface;
use Psr\Container\ContainerInterface;
use Dhii\Util\String\StringableInterface as Stringable;

/**
 * Functionality for rendering an internal template with internal context.
 *
 * @since [*next-version*]
 */
trait RenderCapableTemplateBlockTrait
{
    /**
     * Renders an internal template.
     *
     * @since [*next-version*]
     *
     * @throws CouldNotRenderExceptionInterface If the template could not be rendered.
     * @throws RendererExceptionInterface       If a problem related to a renderer occurs.
     */
    protected function _render()
    {
        try {
            $template = $this->_getTemplate();
            $context  = $this->_getContextFor($template);

            return $this->_renderTemplate($template, $context);
        } catch (RootException $e) {
            throw $this->_throwCouldNotRenderException($this->__('Could not render template'), null, $e);
        }
    }

    /**
     * Retrieves the template associated with this instance.
     *
     * @since [*next-version*]
     *
     * @return TemplateInterface|null The template.
     */
    abstract protected function _getTemplate();

    /**
     * Retrieves a rendering context.
     *
     * @param TemplateInterface $template The template, for which to get the context.
     *
     * @since [*next-version*]
     *
     * @return ContainerInterface|array The context.
     */
    abstract protected function _getContextFor(TemplateInterface $template);

    /**
     * Renders a template with context.
     *
     * @param TemplateInterface             $template The template to render.
     * @param ContainerInterface|array|null $context  The context to use for rendering.
     *
     * @since [*next-version*]
     *
     * @throws TemplateRenderExceptionInterface The template may throw this if a problem occurs.
     * @throws RendererExceptionInterface       The template may throw this if a problem specific to rendering occurs.
     *
     * @return string|Stringable The rendered output.
     */
    abstract protected function _renderTemplate(TemplateInterface $template, $context = null);

    /**
     * Throws a new render failure exception.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable|null $message  The error message, if any.
     * @param int|null               $code     The error code, if any.
     * @param RootException|null     $previous The inner exception for chaining, if any.
     * @param RendererInterface      $renderer The associated renderer, if any.
     *
     * @return CouldNotRenderExceptionInterface The new exception.
     */
    abstract protected function _throwCouldNotRenderException(
        $message = null,
        $code = null,
        RootException $previous = null,
        RendererInterface $renderer = null
    );

    /**
     * Translates a string, and replaces placeholders.
     *
     * @since [*next-version*]
     * @see sprintf()
     *
     * @param string $string  The format string to translate.
     * @param array  $args    Placeholder values to replace in the string.
     * @param mixed  $context The context for translation.
     *
     * @return string The translated string.
     */
    abstract protected function __($string, $args = [], $context = null);
}
