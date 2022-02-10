<?php

namespace Dhii\Output\Exception;

use ArrayAccess;
use Dhii\Data\Container\NormalizeContainerCapableTrait;
use Exception as RootException;
use Dhii\Util\String\StringableInterface as Stringable;
use Psr\Container\ContainerInterface;
use Dhii\Output\TemplateInterface;
use Dhii\Output\RendererAwareTrait;
use Dhii\Output\ContextAwareTrait;
use Dhii\I18n\StringTranslatingTrait;
use Dhii\Exception\CreateInvalidArgumentExceptionCapableTrait;
use stdClass;

/**
 * An exception that occurs when a template cannot produce output.
 *
 * @since [*next-version*]
 */
class TemplateRenderException extends RootException implements TemplateRenderExceptionInterface
{
    /*
     * Adds internal renderer awareness.
     *
     * @since [*next-version*]
     */
    use RendererAwareTrait {
        RendererAwareTrait::_setRenderer as _setRendererOriginal;
    }

    /*
     * Adds internal context awareness.
     *
     * @since [*next-version*]
     */
    use ContextAwareTrait;

    /*
     * Adds internal i18n capabilities.
     *
     * @since [*next-version*]
     */
    use StringTranslatingTrait;

    /*
     * Adds internal factory for creating invalid argument exceptions.
     *
     * @since [*next-version*]
     */
    use CreateInvalidArgumentExceptionCapableTrait;

    /*
     * Adds container normalization capabilities.
     *
     * @since [*next-version*]
     */
    use NormalizeContainerCapableTrait;

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable|null                             $message  The error message, if any.
     * @param int|null                                           $code     The error code, if any.
     * @param RootException|null                                 $previous The inner exception, if any.
     * @param TemplateInterface|null                             $renderer The associated renderer, if any.
     * @param array|ArrayAccess|stdClass|ContainerInterface|null $context  The associated context, if any.
     */
    public function __construct($message = null, $code = null, RootException $previous = null, TemplateInterface $renderer = null, $context = null)
    {
        parent::__construct((string) $message, (int) $code, $previous);
        $this->_setRenderer($renderer);
        $this->_setContext($context);
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function getRenderer()
    {
        return $this->_getRenderer();
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function getContext()
    {
        return $this->_getContext();
    }

    /**
     * {@inheritdoc}
     *
     * The renderer must be a template.
     *
     * @since [*next-version*]
     */
    protected function _setRenderer($renderer)
    {
        if ($renderer !== null && !($renderer instanceof TemplateInterface)) {
            throw $this->_createInvalidArgumentException(
                $this->__('Invalid template'),
                null,
                null,
                $renderer
            );
        }

        $this->_setRendererOriginal($renderer);
    }
}
